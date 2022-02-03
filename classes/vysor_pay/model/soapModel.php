<?php
#include "../../lib/webservices.php";
/*
WSDL SOAP: https://homologacao.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl
WSDL REST: https://homologacao.superpay.com.br/superpay/v1/transacao?_wadl

OLD
https://homologacao.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl

*/
class soapModel{
	public $USUARIO_SUPERPAY = ''; //Informado pelo suporte
	public $SENHA_SUPERPAY = ''; //Informado pelo suporte
	/*var $SOAP_URL = "https://homologacao.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl";
	var $URL_OCLK = "https://homologacao.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl";
	var $RESP_URL = "https://homologacao.superpay.com.br/superpay/v1/transacao?_wadl";
	var $URL_SREC = "http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl";
	*/
	
	//! ****** Ambiente de Teste
	//var $servicosRecorrenciaWS = "http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl";
	//var $servicosPagamentoCompletoWS= "https://homologacao.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl";
	//var $servicosPagamentoOneClickWS= "https://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl";
	
	/***
	
	https://superpay2.superpay.com.br/checkout/servicosPagamentoCompletoWS.Services?wsdl (envio de pagamento via SOAP) 

	https://superpay2.superpay.com.br/checkout/servicosRecorrenciaWS.Services?wsdl (envio de Recorrência) 

	https://superpay2.superpay.com.br/checkout/v1/transacao?_wadl (envio de pagamento via REST) 

	https://superpay2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl (envio de pagamento OneClick)
	**/
	
	var $servicosRecorrenciaWS = "https://gateway.yapay.com.br/checkout/servicosRecorrenciaWS.Services?wsdl";
	//"https://superpay2.superpay.com.br/checkout/servicosRecorrenciaWS.Services?wsdl";
	var $servicosPagamentoCompletoWS= "https://gateway.yapay.com.br/checkout/servicosPagamentoCompletoWS.Services?wsdl";
	//"https://superpay2.superpay.com.br/checkout/servicosPagamentoCompletoWS.Services?wsdl";
	var $servicosPagamentoOneClickWS= "https://gateway.yapay.com.br/checkout/servicosPagamentoOneClickWS.Services?wsdl";
	//"https://superpay2.superpay.com.br/checkout/servicosPagamentoOneClickWS.Services?wsdl";
	
	var $servicosRecorrenciaWS2 = "https://homologacao.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl";
	
	function consultaTransacaoEspecifica($dados_envio){
		$parametros = array('consultaTransacaoWS'=>$dados_envio, 'usuario'=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'consultaTransacaoEspecifica';
		//$retorno = callWebServices($parametros,$funcao_chamada,"https://homologacao2.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl");
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoCompletoWS);
		return $retorno;		
	}
	
	function consultaTransacao($dados_envio){	
		//echo($this->servicosPagamentoCompletoWS);
		$parametros = array('consultaTransacaoWS'=>$dados_envio, 'usuario'=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY, 'numeroTransacao'=>$_GET['numeroTransacao']);
		$funcao_chamada = 'consultaTransacao';	
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoCompletoWS);
		return $retorno;		
	}

	function pagamentoCompleto($dados_envio){
		$parametros = array('transacao'=>$dados_envio, 'usuario'=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'pagamentoTransacaoCompleta';
		//$retorno = callWebServices($parametros,$funcao_chamada,"https://homologacao.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl");
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoCompletoWS);
		return $retorno;
	}
	
	function pagamentoCompletoMaisCartao($dados_envio){
		$parametros = array('transacao'=>$dados_envio, 'usuario'=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'pagamentoTransacaoCompletaMaisCartoesCredito';
		//$retorno = callWebServices($parametros,$funcao_chamada,"https://homologacao.superpay.com.br/superpay/servicosPagamentoCompletoWS.Services?wsdl");
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoCompletoWS);
		return $retorno;
	}
	
	function operacaoTransacao($dados_envio){
		$parametros = array('operacao'=>$dados_envio, 'usuario'=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'operacaoTransacao';
		//$retorno = callWebServices($parametros,$funcao_chamada, $this->URL_SREC);
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoCompletoWS);
		
		return $retorno;
	}
	
	function criarRecorrencia($dados_envio){		
		$parametros = array('recorrenciaWS'=>$dados_envio, 'usuario'=>array("usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY));
		$funcao_chamada = 'cadastrarRecorrenciaWS';
		$retorno = callWebServices($parametros,$funcao_chamada, $this->servicosRecorrenciaWS );
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl");
		return $retorno;
	}
	
	function consultarRecorrencia($dados_envio){		
		$parametros = array('recorrenciaConsultaWS'=>$dados_envio, 'usuario'=>array("usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY));
		$funcao_chamada = 'consultaTransacaoRecorrenciaWS';
		$retorno = callWebServices($parametros,$funcao_chamada, $this->servicosRecorrenciaWS2 );
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl");
		return $retorno;
	}
	
	function cancelarRecorrencia($dados_envio){
		$parametros = array('recorrenciaCancelarWS'=>$dados_envio, 'usuario'=>array("usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY));
		$funcao_chamada = 'cancelarRecorrenciaWS';
		$retorno = callWebServices($parametros,$funcao_chamada, $this->servicosRecorrenciaWS2 );
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosRecorrenciaWS.Services?wsdl");
		return $retorno;
	}
	
	function cadastraPagamentoOneClick($dados_envio){
		$parametros = array("dadosOneClick"=>$dados_envio, "usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'cadastraPagamentoOneClickV2';
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoOneClickWS);
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
		//$retorno = callWebServices($parametros,$funcao_chamada, $this->URL_OCLK);
		return $retorno;
	}
	
	function cadastraPagamentoOneClickOLD($dados_envio){
		$parametros = array("dadosOneClick"=>$dados_envio, "usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'cadastraPagamentoOneClick';
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoOneClickWS);
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
		//$retorno = callWebServices($parametros,$funcao_chamada, $this->URL_OCLK);
		return $retorno;
	}
	
	function consultaDadosOneClick($dados_envio){
		$parametros = array('token'=>$dados_envio, "usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'consultaDadosOneClick';
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoOneClickWS);
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
		return $retorno;
	}
	
	function alteraCadastraPagamentoOneClick($dados_envio,$token){
		$parametros = array('dadosOneClick'=>$dados_envio,'token'=>$token, "usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'alteraCadastraPagamentoOneClick';		
		
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoOneClickWS);
		
		//$retorno = callWebServices($parametros,$funcao_chamada, "https://homologacao.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl" );			
		//$retorno = callWebServices($parametros,$funcao_chamada, "http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
		return $retorno;
	}
	
	function pagamentoOneClick($dados_envio){
		$parametros = array('transacao'=>$dados_envio,"usuario"=>$this->USUARIO_SUPERPAY, 'senha'=>$this->SENHA_SUPERPAY);
		$funcao_chamada = 'pagamentoOneClickV2';
		$retorno = callWebServices($parametros,$funcao_chamada,$this->servicosPagamentoOneClickWS);
		//$retorno = callWebServices($parametros,$funcao_chamada,"http://homologacao2.superpay.com.br/superpay/servicosPagamentoOneClickWS.Services?wsdl");
		return $retorno;
	}
}
?>