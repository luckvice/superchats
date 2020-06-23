<?php namespace App\Controllers;

class Home extends BaseController
{

	/*

		[Pagina inicial do SuperChats]

	*/ 
	public function index()
	{
		$sessao = \Config\Services::session();

		if($sessao->get('logou') == null){
			$data['sessaoChat'] = view('modal/login_view');//Se nao tiver logado Não carrega as sessoes do chat
		}else{
			$data['nickname'] 	= $sessao->get('nickname');
			$data['img']		= $sessao->get('img');
			$data['sessaoChat'] = view('modal/modal_view',$data);
		}
	
		return view('home', $data);
	}

	public function logar_usuario(){
		$request 	= \Config\Services::request();
		$sessao 	= \Config\Services::session();
		
	

		$sessao->set('nickname',$request->getPost('nickname'));
		$sessao->set('img',$request->getPost('img'));
		$sessao->set('logou',true);
		return redirect()->to('/superchats/public');
	}

	public function deslogar(){
		$sessao = \Config\Services::session();
		$sessao->destroy();
		return redirect()->to('/superchats/public');
	}
}
