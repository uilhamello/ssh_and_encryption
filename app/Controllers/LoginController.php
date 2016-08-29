<?php

class LoginController extends Controller{

	private $user;

	public function __construct()
	{
		$this->user = new User;
	}


	public function index(){
	
		return ['teste'=>'coisas'];
	}

	public function register_view()
	{

	}

	public function register()
	{
		$result = $this->user->select()->where('username',$_POST['username'])->get();

		if($result){
			return [
					'message' => 'usuario já registrado. Ir para <a href=\'{{URL_BASE}}\'>Login</a>',
					'alert-class' => 'alert-danger',
				];
		}

		$password_plaintext = $_POST['password'];
		$_POST['password'] = Crypt::hash($_POST['password']);

		if(!($id = $this->user->insert($_POST))){
			return [
					'message' => 'Erro ao tentar cadastrar.',
					'alert-class' => 'alert-danger',
				];

		}

		if($this->startSession($_POST['username'],$password_plaintext)){
			redirect_route('dashboard');
		}else{
			return [
					'message' => 'Erro ao iniciar sessão.',
					'alert-class' => 'alert-danger',
				];
		}


	}

	public function startSession($username, $password)
	{
		$user = $this->user->select()
				->where('username',$username)
				->get();
		//User not exists
		if(!$user){
			return false;
		}

		//Password is not correct
		if(!Crypt::check($password, $user[0]['password'])){
			return false;			
		}

		$_SESSION['name'] = $user[0]['name'];
		$_SESSION['username'] = $user[0]['username'];
		$_SESSION['password'] = $user[0]['password'];
		$_SESSION['user_id'] = $user[0]['id'];

		return true;
	}

	public function logout()
	{
		if(isset($_SESSION['user_id'])){
			session_destroy();
		}
		//Se conectado, redireciona para dashboard
		redirect_route('login');
	}

	public function login()
	{

		if(empty($_POST['username']) || empty($_POST['password']) ){

			View::data([
				'message' => 'Dados não informados.',
				'alert-class' => 'alert-danger',
			]);
			view('login.html');
			return false;
		}
		if(!$this->startSession($_POST['username'], $_POST['password'])){
			View::data([
				'message' => 'Usuário não encontrado.',
				'alert-class' => 'alert-danger',
			]);
			view('login.html');
			return false;
		}

		//Se conectado, redireciona para dashboard
		redirect_route('dashboard');
	}
}