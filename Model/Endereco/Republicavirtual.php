<?php
namespace Bleez\Correios\Model\Endereco;
/**
 * Class Republicavirtual
 * Traz endereço do cep usando webservice Republica Virtal
 * @package Bleez\Correios\Model\Endereco
 */
class Republicavirtual
{

	/**
	 * @param string $cep
	 * @return array|bool
	 */
	public static function getEndereco($cep)
	{
		$html = self::_request('http://cep.republicavirtual.com.br/web_cep.php?cep='.$cep.'&formato=json');
		
		$json = json_decode($html, 1);
		
		if($json){
			$dados = array(
				'logradouro' => $json['tipo_logradouro'].' '.$json['logradouro'],
				'bairro' => $json['bairro'],
				'cep' => $cep,
				'cidade' => $json['cidade'],
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
