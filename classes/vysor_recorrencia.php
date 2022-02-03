<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
require_once ('vysor_pay/lib/nusoap.php');
require_once ('vysor_pay/lib/webservices.php');
require_once ('vysor_pay/model/soapModel.php');
$soap = new soapModel();

//include("teste.php");
if(isset($_GET['versao']))
{
	//echo("18.FEV.2016 - Altera��o 001;");
	echo("29.AGO.2018 - Altera��o 002;");
	exit;
}
if(isset($_GET['call']))
{
	$soap->USUARIO_SUPERPAY = $_GET['vysorpay_usuario_superpay'];
	$soap->SENHA_SUPERPAY   = $_GET['vysorpay_senha_superpay'];
	if($soap->USUARIO_SUPERPAY=="" || $soap->SENHA_SUPERPAY=="")
	{
		echo("Necess�rio Usu�rio ou Senha!");
		exit;
	}	

	$call = $_GET['call'];
	switch($call)
	{
		case 'cadastraPagamentoOneClick':
		case 'alteraCadastraPagamentoOneClick': //op��o est� dando erro checkar ($soap->alteraCadastraPagamentoOneClick)
			$dados['codigoEstabelecimento'] 		= $_GET['codigoEstabelecimento'];
			$dados['nomeTitularCartaoCredito'] 	= $_GET['nomeTitularCartaoCredito'];
			$dados['numeroCartaoCredito'] 		= $_GET['numeroCartaoCredito'] ;
			// $dados['codigoSeguranca'] 			= $_GET['codigoSeguranca'];
			$dados['dataValidadeCartao'] 	= $_GET['dataValidadeCartao'];
			$dados['emailComprador']		= $_GET['emailComprador'];
			$dados['formaPagamento']		= $_GET['formaPagamento'];			
			
			
			if($call == 'cadastraPagamentoOneClick')
			{
				$retorno = $soap->cadastraPagamentoOneClick($dados);
				$R = $retorno['return'];
				if($R != '<')
					echo("TOKKEN=".$R );
				else 
					print_r($retorno);
			}
			
			else if($call == 'alteraCadastraPagamentoOneClick')
			{
				$tokken = $_GET['tokken'];
				$retorno = $soap->alteraCadastraPagamentoOneClick($dados, $tokken );
				print_r($retorno);
			}		
			
			//print_r($retorno);
			break;
			
		case 'pagamentoTransacaoCompleta':
		case 'pagamentoOneClick':	
			$dados['numeroTransacao'] 		= $_GET['numeroTransacao'];
			$dados['codigoEstabelecimento'] 	= $_GET['codigoEstabelecimento'];
			$dados['token'] 		= $_GET['token'];
			$dados['cvv'] 			= $_GET['cvv'];
			$dados['valor'] 		= $_GET['valor'];
			$dados['valorDesconto'] 	= $_GET['valorDesconto'];
			$dados['taxaEmbarque']	= $_GET['taxaEmbarque'];
			$dados['parcelas'] 		= $_GET['parcelas'];
			$dados['vencimentoBoleto']		= $_GET['vencimentoBoleto'];
			$dados['urlCampainha']	= $_GET['urlCampainha'];
			$dados['urlRedirecionamentoPago']	= $_GET['urlRedirecionamentoPago'];
			$dados['urlRedirecionamentoNaoPago']	= $_GET['urlRedirecionamentoNaoPago'];
			$dados['origemTransacao']= $_GET['origemTransacao'];
			$dados['campoLivre1']	= $_GET['campoLivre1'];
			$dados['campoLivre2']	= $_GET['campoLivre2'];
			$dados['campoLivre3']	= $_GET['campoLivre3'];
			$dados['campoLivre4']	= $_GET['campoLivre4'];
			$dados['campoLivre5']	= $_GET['campoLivre5'];
			$dados['ip']	= $_GET['ip'];
			$dados['idioma'] 	= $_GET['idioma'];
			
			if (isset($_GET['dadosUsuarioTransacao']))
			{
				$dados["dadosUsuarioTransacao"]["bairroEnderecoComprador"] = $_GET['bairroEnderecoComprador'];
				$dados["dadosUsuarioTransacao"]["bairroEnderecoEntrega"] =  $_GET['bairroEnderecoEntrega'];
				$dados["dadosUsuarioTransacao"]["cepEnderecoComprador"] =  $_GET['cepEnderecoComprador'];
				$dados["dadosUsuarioTransacao"]["cepEnderecoEntrega"] =  $_GET['cepEnderecoEntrega'];
				$dados["dadosUsuarioTransacao"]["cidadeEnderecoComprador"] =  $_GET['cidadeEnderecoComprador'];
				$dados["dadosUsuarioTransacao"]["cidadeEnderecoEntrega"] =  $_GET['cidadeEnderecoEntrega'];
				$dados["dadosUsuarioTransacao"]["codigoCliente"] =  $_GET['codigoCliente'];
				//Caso n�o haja tipo de telefone; envi�-lo nulo sem aspas conforme mostrado no exemplo codigoTipoTelefoneAdicionalComprador
				$dados["dadosUsuarioTransacao"]["codigoTipoTelefoneAdicionalComprador"];
				$dados["dadosUsuarioTransacao"]["codigoTipoTelefoneAdicionalEntrega"];
				$dados["dadosUsuarioTransacao"]["codigoTipoTelefoneComprador"] = $_GET['codigoTipoTelefoneComprador'];
				$dados["dadosUsuarioTransacao"]["codigoTipoTelefoneEntrega"] = $_GET['codigoTipoTelefoneEntrega'];
				$dados["dadosUsuarioTransacao"]["complementoEnderecoComprador"] = $_GET['complementoEnderecoComprador'];
				$dados["dadosUsuarioTransacao"]["complementoEnderecoEntrega"] = $_GET['complementoEnderecoEntrega'];
				//Data de nascimento do comprador (formato dd/mm/yyyy)
				$dados["dadosUsuarioTransacao"]["dataNascimentoComprador"] = $_GET['dataNascimentoComprador'];//"10/10/1965";
				$dados["dadosUsuarioTransacao"]["dddAdicionalComprador"];
				$dados["dadosUsuarioTransacao"]["dddAdicionalEntrega"];
				$dados["dadosUsuarioTransacao"]["dddComprador"] = $_GET['dddComprador'];
				$dados["dadosUsuarioTransacao"]["dddEntrega"] = $_GET['dddEntrega'];
				$dados["dadosUsuarioTransacao"]["ddiAdicionalComprador"];
				$dados["dadosUsuarioTransacao"]["ddiAdicionalEntrega"];
				$dados["dadosUsuarioTransacao"]["ddiComprador"] = $_GET['ddiComprador'];
				$dados["dadosUsuarioTransacao"]["ddiEntrega"] = $_GET['ddiEntrega'];
				// o campo documentoComprador � o documento principal para a identifica��o do comprador como por exemplo cpf/cnpj
				$dados["dadosUsuarioTransacao"]["documentoComprador"] = "";
				// documento secund�rio
				$dados["dadosUsuarioTransacao"]["documento2Comprador"] = $_GET['documento2Comprador'];
				$dados["dadosUsuarioTransacao"]["emailComprador"] = $_GET['emailComprador'];
				$dados["dadosUsuarioTransacao"]["enderecoComprador"] = $_GET['enderecoComprador'];
				$dados["dadosUsuarioTransacao"]["enderecoEntrega"] = $_GET['enderecoEntrega'];
				$dados["dadosUsuarioTransacao"]["estadoEnderecoComprador"] = $_GET['estadoEnderecoComprador'];
				$dados["dadosUsuarioTransacao"]["estadoEnderecoEntrega"] = $_GET['estadoEnderecoEntrega'];
				$dados["dadosUsuarioTransacao"]["nomeComprador"] = $_GET['nomeComprador'];
				$dados["dadosUsuarioTransacao"]["numeroEnderecoComprador"] =  $_GET['numeroEnderecoComprador'];
				$dados["dadosUsuarioTransacao"]["numeroEnderecoEntrega"] = $_GET['numeroEnderecoEntrega'];
				// sexoComprador (m = masculino; f = feminino)
				$dados["dadosUsuarioTransacao"]["sexoComprador"] = $_GET['sexoComprador'];
				$dados["dadosUsuarioTransacao"]["telefoneAdicionalComprador"];
				$dados["dadosUsuarioTransacao"]["telefoneAdicionalEntrega"];
				$dados["dadosUsuarioTransacao"]["telefoneComprador"] = $_GET['telefoneComprador'];
				$dados["dadosUsuarioTransacao"]["telefoneEntrega"] = $_GET['telefoneEntrega'];
				//tipoCliente (1 = F�sica; 2 = Jur�dica)
				$dados["dadosUsuarioTransacao"]["tipoCliente"] = $_GET['tipoCliente'];
				
				if ($MODO_TESTE)
				{
					echo ("TESTE" . $_GET['nomeComprador']); break;
				}
			}
			if (isset($_GET['itensDoPedido']))
			{
				$count = $_GET['itensDoPedido'];
				for($c = 0; $c < $count; $c++)
				{
					//itensDoPedido � um array com os itens do carrinho; no exemplo a seguir foi utilizado a venda de um �nico produto
					$key = "itensDoPedido_".$c."_"; //itensDoPedido_0_tipoCliente
					$dados["itensDoPedido"][$c]["codigoProduto"] 	= $_GET[$key.'codigoProduto']; //"6548";
					$dados["itensDoPedido"][$c]["codigoCategoria"] 	= $_GET[$key.'codigoCategoria']; //"1";
					$dados["itensDoPedido"][$c]["nomeProduto"] 		= $_GET[$key.'nomeProduto']; //"CG-150";
					$dados["itensDoPedido"][$c]["quantidadeProduto"]= $_GET[$key.'quantidadeProduto']; //"1";
					$dados["itensDoPedido"][$c]["valorUnitarioProduto"] = $_GET[$key.'valorUnitarioProduto']; //50000;
					$dados["itensDoPedido"][$c]["nomeCategoria"] 	= $_GET[$key.'nomeCategoria']; //"Auto";
					//fim itensDoPedido
				}
			}			
			
			if($call == 'pagamentoTransacaoCompleta')
			{
				$retorno = $soap->pagamentoCompleto($dados);
				if(is_array ($retorno))
					print_x($retorno);
				else
					echo("ERROR=" . $retorno);
			}			
			else if($call == 'pagamentoOneClick')
			{				
				$retorno = $soap->pagamentoOneClick($dados);

				if(is_array ($retorno))
					print_x($retorno);
				else
					echo("ERROR=" . $retorno);
			}
			else 
			{
				echo("Chamada Invalida [ $call ]");
			}
			break;
		
		case 'resultadoPagamento':	
			$dados['numeroTransacao'] 		= $_GET['numeroTransacao'];
			$dados['codigoEstabelecimento'] 	= $_GET['codigoEstabelecimento'];
			$dados['operacao'] 		= $_GET['operacao'];
			
			$retorno = $soap->ResultadoPagamento($dados);
			print_r($retorno);
			break;
			
		case 'consultaDadosOneClick':
			//$dados['token'] 	= $_GET['token'];			
			
			$retorno = $soap->consultaDadosOneClick($_GET['token']);
			print_x($retorno);
			break;
		
		case 'operacaoTransacao':		//op��o est� dando erro checkar ($soap->alteraCadastraPagamentoOneClick)
		case 'consultaTransacao':
		case 'consultaTransacaoEspecifica':
			$dados['numeroTransacao'] 	    = $_GET['numeroTransacao'];
			$dados['codigoEstabelecimento'] = $_GET['codigoEstabelecimento'];
			
			if($call == 'consultaTransacaoEspecifica')
			{				
				$retorno = $soap->consultaTransacaoEspecifica($dados);
				print_x($retorno);
			}
			if($call == 'consultaTransacao')
			{				
				$retorno = $soap->consultaTransacao($dados);
				if(is_array ($retorno))
					print_x($retorno);
				else 
					echo("ERROR [$call]=" . $retorno);
				
			}
			else if($call == 'operacaoTransacao')
			{
				$dados['operacao'] 	= $_GET['operacao'];
				$retorno = $soap->operacaoTransacao($dados);
				if(is_array ($retorno))
					print_x($retorno);
				else
					echo("ERROR=" . $retorno);
				///print_x($retorno);
			}
			break;
		
		case 'cancelarTransacao':
			$dados['numeroTransacao'] 	    = $_GET['numeroTransacao'];
			$dados['codigoEstabelecimento'] = "1408985159357";
			//echo('teste');
			$retorno = $soap->cancelarRecorrencia($dados);
			///print_r($retorno);
			if(is_array ($retorno))
				print_r($retorno);
			else
				echo("ERROR=" . $retorno);
			break;
		
		default:
			echo("fail".$call);
	}
}
else echo("fail");


function print_x($arrayreturn)
{
	foreach ($arrayreturn as $key => $value) 
	{
		//echo "$key= $value <br />";
		foreach ($value as $keyIntro => $valueIntro) 
		{
			echo("$keyIntro = $valueIntro ;</*breack*/>;");
		}
	}
}
?>