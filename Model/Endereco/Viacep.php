<?php
namespace Bleez\Correios\Model\Endereco;
/**
 * Class Viacep
 * Traz endereço do cep usando webservice via cep
 * @package Bleez\Correios\Model\Endereco
 */
class Viacep
{

	/**
	 * @param string $cep
	 * @return array|bool
	 */
	public static function getEndereco($cep)
	{
		$html = self::_request('http://viacep.com.br/ws/'.$cep.'/json/');
		
		$json = json_decode($html, 1);
		
		if($json){
			$dados = array(
				'logradouro' => $json['logradouro'],
				'bairro' => $json['bairro'],
				'cep' => (int)$cep,
				'cidade' => $json['localidade'],
				'uf' => strtoupper($json['uf'])
			);
			
			if(strpos($dados['logradouro'], ' - ') !== false){
				$l = explode(' - ', $dados['logradouro']);
				$dados['logradouro'] = $l[0];
			}
			
			return $dados;
		}
		
		return false;
	}

	/**
	 * @param string $url
	 * @param array $get
	 * @return mixed
	 */
	public static function _request($url, $get=array())
	{
		$ch = curl_init($url);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		return curl_exec($ch);
	}
	
}
