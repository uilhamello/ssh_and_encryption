<?php

class MachineController extends Controller{

	public function __construct()
	{
		$this->model = new Machine();
	}

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index()
	{
		
		$result = $this->model->select()->where('user_id', $_SESSION['user_id'])->get();

		if(!$result){
		    View::data(['table_content'=>'']);
		} else {

			$table = '';
			foreach ($result as $key => $value) {
			$table .= '<tr>';
				$table .= '<td>'.$value['ip'].'</td>';
				$table .= '<td>'.$value['user'].'</td>';
				$table .= '<td><a href=\'{{URL_BASE}}/?module=ssh_connection&machine='.($value['id']).'\'>conectar</td>';				
			$table .= '</tr>';
			}
			
		    View::data(['table_content'=>$table]);			
		}
	}

	/**
	 * [insert_conection description]
	 * @return [type] [description]
	 */
	public function insert_conection()
	{
		if(!$this->check_connection($_POST['ip'],$_POST['user'],$_POST['password'])){
			View::old_record();
			redirect_route('list_connection_ssh');
		}

		if(!($id = $this->model->insert($_POST))){
			return [
					'message' => 'Erro ao tentar cadastrar a conexão.',
					'alert-class' => 'alert-danger',
				];
		}

		$_SESSION['machine_id'] = $id;
		redirect_route('ssh_connection');
	}

	/**
	 * [connection description]
	 * @return [type] [description]
	 */
	public function shell()
	{
		$id = false;
		if(isset($_GET['machine'])){
			$id = $_GET['machine'];
		}
		elseif(isset($_SESSION['machine_id'])){
			$id = $_SESSION['machine_id'];
		}

		if(!$id){
			$_SESSION['view_to_message'] = [
				'message'=>'Error: Id da maquina não informado.',
				'alert-class'=>'alert-warning'];			
			redirect_route('list_connection_ssh');
		}

		$machine = $this->model->find($id);
		$machine = $machine[0];

		if(!$machine){
			$_SESSION['view_to_message'] = [
				'message'=>'Error: Id da maquina não pode ser encontrado.',
				'alert-class'=>'alert-warning'];			
			redirect_route('list_connection_ssh');
		}

		if(!Ssh2::check_suport()){
			View::data(['message'=>'Esta máquina não tem suporte para \'ssh2_connect\'',
				'alert-class'=>'alert-warning']);		
		} else {
			if(!$this->check_connection($machine['ip'],$machine['user'],$machine['password'])){
				die('aaees');
				redirect_route('list_connection_ssh');
			}else{
				$_SESSION['view_to_message'] = [
					'message'=>'Olá, <b>'.strtoupper($machine['user']).'</b>. Seja Bem Vindo. Você está em <b>'.strtoupper($machine['ip'])."</b> via SSH",
					'alert-class'=>'alert-info'];
			}

		}
		View::data($machine);
		view('machine/shell.html');
	}

	public function check_connection($ip,$user,$password,$port='22')
	{
		if(!Ssh2::connect($ip, $user, $password, $port)){

			$_SESSION['view_to_message'] = [
				'message'=>'Error: Imposible to connect: '.Ssh2::$error,
				'alert-class'=>'alert-warning'];

			return false;
		}
		return true;
	}

	public function execute_command()
	{
		$id = $_POST['machine'];
		$command = $_POST['command'];
		$machine = $this->model->find($id);
		$machine = $machine[0];

		//Connect and Return result
		if(!Ssh2::connect($machine['ip'], $machine['user'], $machine['password'])){
			echo "Erro ao se conectar";
		}else{
			echo Ssh2::command($command);
		}

		//Store commands to show if press 'Up/Down'
		$_SESSION['command'][] = $command;

		exit;
	}

	public function command_updown()
	{
		if($_POST['direction'] == 'up'){
			if(isset($_SESSION['command'][ $_POST['position'] + 1 ])){
				echo $_SESSION['command'][ $_POST['position'] + 1 ];
			}
		}else{
			if(isset($_SESSION['command'][ $_POST['position'] - 1 ])){
				echo $_SESSION['command'][ $_POST['position'] - 1 ];
			}
		}
	}
}