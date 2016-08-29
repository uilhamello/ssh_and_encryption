<?php
/**
 * 
 */
class EncryptedTextController extends Controller{

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index()
	{}

	/**
	 * [crypt_text description]
	 * @return [type] [description]
	 */
	public function crypt_text()
	{
		if(isset($_POST['texto'])){

			if($_POST['action'] == 'encrypt'){

				$encrypted = Crypt::enc($_POST['texto']);
				$data = [
				'texto' => $_POST['texto'],
				'encrypted' => $encrypted,
				'key' => Crypt::getHashString(),
				'decrypted' => Crypt::denc($encrypted, Crypt::getHashString())
				];
			}
			elseif($_POST['action'] == 'decrypt'){
				$data = [
				'texto' => $_POST['texto'],
				'key' => $_POST['key'],
				'decrypted' => Crypt::denc($_POST['texto'], Crypt::getHashString())
				];
			}

			View::data($data);
		}

		return false;
	}
}