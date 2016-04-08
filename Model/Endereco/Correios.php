<?php
namespace Bleez\Correios\Model\Endereco;

use PhpQuery\PhpQuery as phpQuery;

/**
 * Class Correios
 * Traz endereço do cep usando site dos correios
 * @package Bleez\Correios\Model\Endereco
 */
class Correios
{
	/**
	 * @param string $cep
	 * @return array|bool
	 */

	public static function getEndereco($cep)
	{

		$html = self::_request('http://m.correios.com.br/movel/buscaCepConfirma.do', array(
			'cepEntrada' => $cep,
			'tipoCep' => '',
			'cepTemp' => '',
			'metodo' => 'buscarCep'
		));
		
		if($html && strpos($html, 'Error') === false && strpos($html, 'Dados nao encontrados') === false){

			$phpQuery = phpQuery::newDocumentHTML($html, $charset = 'utf-8');

			$dados = array(
				'logradouro'=> trim($phpQuery->find('.caixacampobranco .resposta:contains("Logradouro: ") + .respostadestaque:eq(0)')->html()),
				'bairro'=> trim($phpQuery->find('.caixacampobranco .resposta:contains("Bairro: ") + .respostadestaque:eq(0)')->html()),
				'cidade/uf'=> trim($phpQuery->find('.caixacampobranco .resposta:contains("Localidade / UF: ") + .respostadestaque:eq(0)')->html()),
				'cep'=> trim($phpQuery->find('.caixacampobranco .resposta:contains("CEP: ") + .respostadestaque:eq(0)')->html())
			);
			
			if(empty($dados['cidade/uf'])){
				return false;
			}
			
			$dados['cidade/uf'] = explode('/',$dados['cidade/uf']);
			$dados['cidade'] = trim($dados['cidade/uf'][0]);
			$dados['uf'] = trim($dados['cidade/uf'][1]);

			unset($dados['cidade/uf']);
			
			if(strpos($dados['logradouro'], ' - ') !== false){
				$l = explode(' - ', $dados['logradouro']);
				$dados['logradouro'] = $l[0];
			}

			$dados['logradouro'] = $dados['logradouro'];
			return $dados;
		}
		
		return false;
	}

	/**
	 * @param string $url
	 * @param array $post
	 * @param array $get
	 * @return mixed
	 */
	public static function _request($url, $post=array(), $get=array())
	{
		$url = explode('?',$url,2);
		
		if(count($url)===2){
			$temp_get = array();
			parse_str($url[1],$temp_get);
			$get = array_merge($get,$temp_get);
		}

		$ch = curl_init($url[0]."?".http_build_query($get));
		
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		return curl_exec($ch);
	}
	
}
