<?php

class UserFileController extends Controller{

	public function __construct()
	{
		$this->model = new UserFile();
	}

	public function index()
	{

		$result = $this->model->select()
		->where('user_id', $_SESSION['user_id'])
		->orderby(['id'=>'desc'])
		->get();
		if(!$result){
		    View::data(['table_content'=>'']);
		} else {

			$table = '';
			foreach ($result as $key => $value) {
			$table .= '<tr>';
				$table .= '<td>'.$value['id'].'</td>';
				$table .= '<td>'.$value['name'].'</td>';
				$table .= '<td>'.$value['modified'].'</td>';
				$table .= '<td>'.$value['created_at'].'</td>';
				$table .= '<td>'.$value['accessed'].'</td>';
				$table .= '<td>'.$value['last_auditing'].'</td>';
				$table .= '<td>'.$value['status'].'</td>';
				$table .= '<td><a href=\'{{URL_BASE}}/?module=upload_file_auditing&id='.($value['id']).'\'>Auditar</td>';				
			$table .= '</tr>';
			}
	
		    View::data(['table_content'=>$table]);			

		}

	    //Session message
		if(isset($_SESSION['message_auditing']) 
			&& !empty($_SESSION['message_auditing'])){
		    View::data($_SESSION['message_auditing']);
		    unset($_SESSION['message_auditing']);
		}else{
		    View::data(['message_auditing' => '']);
		}

		if(isset($_SESSION['message_updated_success'])){
		    View::data($_SESSION['message_updated_success']);
		    unset($_SESSION['message_updated_success']);
		}

	}

	public function uploadFile()
	{
		if(isset($_FILES) and isset($_POST['user_id'])){
			//set file
	        File::move_to('upload/');
			File::setFile(current($_FILES));

			//Hash of $_FILES['tmp_name']
			$hash = File::hash_file();

			$auditing_array = $this->auditing($hash);

			$no_problem = true;
			if($auditing_array != 'ok'){
				$no_problem = false;
				$this->preper_auditing_message($auditing_array);
				//Has any problem but there ever is a original one, so go back to list
				// and inform the 'Auditing'
				if($auditing_array['original']){
					return $this->index();
				}

			}

			//Upload file
			if(!$result = File::upload()){
				View::data( 
					[
						'message' => 'Erro ao tentar Fazer Upload do Arquivo.',
						'alert-class' => 'alert-danger',
				]);
				return $this->index();
			}

			//Insert the FIle information at database
			$_POST['file'] = $result;
			$_POST['hash_original'] = $hash;
			$_POST['status'] = 'iniciado';
			$_POST['file_original_name'] = File::getFile('name');
			if(!($id = $this->model->insert($_POST))){
				View::data(
					[
						'message' => 'Erro ao tentar cadastrar o arquivo no banco de dados.',
						'alert-class' => 'alert-danger',
					]);

				return $this->index();
			}

			//If everything was ok, just inform it!
			if($no_problem){

				$_SESSION['message_updated_success'] = [
						'message' => 'Arquivo armazenado com sucesso',
						'alert-cls' => 'alert-info',
					];
			}

		}

		redirect_route('upload_file_list');
	}

	/**
	 * [auditing description]
	 * @param  [type] $hash [description]
	 * @param  [type] $id   [description]
	 * @return [type]       [description]
	 */
	public function auditing($hash=NULL, $id=NULL)
	{

		if(!empty($hash)){
			//Looking for files with same hash in the database
			$result = $this->model->select()
			->where('hash_original', $hash)
			->where('user_id', $_SESSION['user_id'])->get();

		}
		elseif(!empty($id)){
			//Looking for files with same hash in the database
			$result = $this->model->find($id);			
			$hash = $result[0]['hash_original'];
		}else{
			return false;
		}

		//If exist
		if($result){
			//Loopin through each register and check if it is as same as the $_FILE[] one 
			$audit_array['original']=0;
			$audit_array['changed']=0;
			$audit_array['deleted']=0;
			$audit_array['auditing']= [];
			foreach ($result as $key => $register) {
				
				//If exits at database but no in the directory
				if(!file_exists($register['file'])){
					$file = new UserFile();
					$file->update([
									'status_id' => '1',
									'status' => 'excluido'])
					     ->where('id',$register['id'])
					     ->get();

					     //Get ones which was deleted to Audit
					   $audit_array['auditing'][] = ['status_id'=>'1','status'=>'excluido','name'=>$register['name'],'id'=>$register['id'],'created_at'=>$register['created_at'],'updated_at'=>$register['updated_at']];
					   $audit_array['deleted'] = (int)$audit_array['deleted'] + 1;
				//Else, in other words, if it exist yet
				//check if its content was not changed
				} else {
					//checks whether the file is original
					$current_hash = File::hash_file($register['file']);
					$file = new UserFile();
					if($hash != $current_hash){
						$file->update([
										'status_id' => 2,
										'status' => 'alterado',
										'current_hash' => $current_hash,]
										)
						     ->where('id',$register['id'])
						     ->get();

						     //Get ones which was deleted to Audit
						     $audit_array['auditing'][] = ['status_id'=>'2','status'=>'alterado','name'=>$register['name'],'id'=>$register['id'],'created_at'=>$register['created_at'],'updated_at'=>$register['updated_at']];
						     $audit_array['changed'] = (int)$audit_array['changed'] + 1;
					} else {
						$file->update([
								'status_id' => 3,
								'status' => 'original'])
						     ->where('id',$register['id'])
						     ->get();

						//If exists and it was not changed, in other words if it is original
						    $audit_array['auditing'][] = ['status_id'=>'3','status'=>'original','name'=>$register['name'],'id'=>$register['id'],'created_at'=>$register['created_at'],'updated_at'=>$register['updated_at']];
						   $audit_array['original'] = (int)$audit_array['original'] + 1;
					}
				}

			}

			return $audit_array;			

		} else{
			return 'ok';
		}

	}

	public function preper_auditing_message($auditing_array, $auditing = false)
	{
		$count =0;
		$alterado = "";
		$original = "";
		$excluido = "";

		$new_inserted = '';
		if(($auditing_array['original'] == 0 ) && !($auditing)){
			$new_inserted = ' <b>Esse Novo arquivo foi registrado com sucesso, porém:</b><br><br>';
		}

		foreach ($auditing_array['auditing'] as $key => $value) {

			if($value['updated_at']){
				$updated = "última auditoria realizada: ".$value['updated_at'].".";
			}else{
				$updated = "Nenhuma auditoria realizada nesse arquivo.";
			}
			$count++;
			if($value['status'] == 'alterado'){
				if($auditing){
					$alterado .= "<p class='msg-auditing_".$count."'> <b>Auditoria:</b> O Arquivo Foi ALTERADO.";
	
				}else{
					$alterado .= "<p class='msg-auditing_".$count."'>".$new_inserted." <b>Auditoria:</b>Arquivo já foi Registrado em ".$value['created_at']." com o nome <b>".$value['name']."</b>. O mesmo foi ALTERADO. ".$updated."</p>";

				}
			}
			elseif($value['status'] == 'original'){
				if($auditing){
					$original .= "<p class='msg-auditing_".$count."'><b>Auditoria:</b> Arquivo ORIGINAL. Foi Criado em ".$value['created_at']." com o nome <b>".$value['name']."</b></p>";

				}else{
					$original .= "<p class='msg-auditing_".$count."'><b>Auditoria:</b> Já existe o arquivo, que foi Registrado em ".$value['created_at']." com o nome <b>".$value['name']."</b>, ".$updated."</p>";

				}

			}
			elseif($value['status'] == 'excluido'){
				if($auditing){
					$excluido .= "<p class='msg-auditing_".$count."'> <b>Auditoria:</b> O Arquivo foi EXCLUIDO!";	
				}else{
					$excluido .= "<p class='msg-auditing_".$count."'>".$new_inserted." <b>Auditoria:</b> Arquivo já foi registrado. O mesmo foi EXCLUIDO. Data da criação do registro: <b>".$value['created_at']."</b> com o nome <b>".$value['name']."</b>, ".$updated."</p>";
				}
			}
		}
		$HTML ='';
		if($count>0){
			$HTML ="<div class='panel panel-danger'>
					  <div class='panel-heading text-left'>Atenção</div>
					  <div class='panel-body text-left'>
					    ".$original.$alterado.$excluido."
					  </div>
					</div>";			
		}

		$_SESSION['message_auditing'] = ['message_auditing' => $HTML];
	}

	public function upload_file_auditing()
	{
		$id = $_GET['id'];
		//Auditing
		$auditing_array = $this->auditing(null,$id);
		//Get File Info
		$register = $this->model->find($id);
		$datas = stat($register[0]['file']);
		//Array To update
		$data_update = [
				'last_auditing' => date('Y-m-d H:i:s'),
				'accessed' => date('Y-m-d H:i:s',$datas['atime']),
				'modified' => date('Y-m-d H:i:s',$datas['atime']),
			];
		//Refresh the last information
		$this->model->update($data_update)
		->where('id', $id)->get();

		//Preper the messages
		if($auditing_array['original'] < 1){
			$this->preper_auditing_message($auditing_array, true);
		}
		else{
			$HTML ="<div class='panel panel-info'>
					  <div class='panel-heading text-left'>Sucesso</div>
					  <div class='panel-body text-left'>
					    Arquivo original, sem nenhum problema encontrado!
					  </div>
					</div>";
			$_SESSION['message_auditing'] = ['message_auditing' => $HTML];
		}

		redirect_route('upload_file_list');
	}

}