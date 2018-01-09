<?php

/**
 * Modelo Contacto que actua como mapper al servicio web de Alegra
 */
class Application_Model_ContactApiMapper
{
	private $_baseUri;
	private $_uri;
	private $_email;
	private $_token;
	private $_client;

	public function __construct()
	{
		//se obtienen los parametros de configuracion para conectarse al API
		$dataBootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		
		//buscamos la configuracion para conectarse al API de Alegra
		$configAlegra = $dataBootstrap->getOption('configAlegra');
		
		//setiamos variables globales de configuracion para conectarse al API
		$this->_baseUri = $configAlegra['uri'];
		$this->_uri = $this->_baseUri . 'contacts';
		$this->_email = $configAlegra['email'];
		$this->_token = $configAlegra['token'];
		
		//Se inicializa objeto que se conecta al API de alegra
		$this->_client = new Zend_Http_Client();
		$this->_client->setUri($this->_uri);
		$this->_client->setConfig(array('timeout' => 30));
		$this->_client->setAuth($this->_email, $this->_token,
						  Zend_Http_Client::AUTH_BASIC);
	}

	/**
	 * Metodo para guardar un contacto (Crear y actualizar)
	 * @param Application_Model_Contact $contact
	 * @return type
	 */
	public function store(Application_Model_Contact $contact)
	{
		//Tipo de contacto...
		$type = array();
		if ($contact->getIsClient())
		{
			$type[] = 'client';
		}
		if ($contact->getIsProvider())
		{
			$type[] = 'provider';
		}

		//Direccion y ciudad
		$address = (object) [
					'address' => $contact->getAddress(),
					'city' => $contact->getCity(),
		];

		$params = array(
			'id' => $contact->getId(),
			'name' => $contact->getName(),
			'identification' => $contact->getIdentification(),
			'phonePrimary' => $contact->getPhoneprimary(),
			'phoneSecondary' => $contact->getPhonesecondary(),
			'fax' => $contact->getFax(),
			'mobile' => $contact->getMobile(),
			'observations' => $contact->getObservations(),
			'email' => $contact->getEmail(),
			'priceList' => empty($contact->getPriceList()) ? null : $contact->getPriceList(),
			'seller' => empty($contact->getSeller()) ? null : $contact->getSeller(),
			'term' => empty($contact->getTerm()) ? null : $contact->getTerm(),
			'address' => $address,
			'type' => $type,
			'internalContacts' => $contact->getInternalContacts(),
		);

		//Contacto nuevo
		if (null === ($id = $contact->getId()))
		{
			$this->_client->setUri($this->_uri);
			$response = $this->_client->setRawData(json_encode($params))->request('POST');
			$_data = $response->getBody();
			$data = json_decode($_data, true);
		}
		//Actualizar contacto
		else
		{
			$this->_client->setUri($this->_uri . "/$id");
			$response = $this->_client->setRawData(json_encode($params))->request('PUT');
			$_data = $response->getBody();
			$data = json_decode($_data, true);
		}
		return $data;
	}

	/**
	 * Metodo para obtener detalle de contacto
	 * 
	 * @param type $id
	 * @return type
	 */
	public function get($id)
	{
		//conexion al API
		$this->_client->setUri($this->_uri . "/$id");
		$response = $this->_client->request('GET');
		$_data = $response->getBody();
		$data = json_decode($_data, true);

		//Si ocurre un error...
		if (isset($data['code']) && $data['code'] !== 200)
		{
			return $data;
		}

		//curamos los datos
		$result = $this->_parseData([$data]);
		$contact = new Application_Model_Contact($result[0]);

		//retornamos array ya listo con contacto individual
		return [
			'data' => $contact
		];
	}
	
	/**
	 * Metodo para obetner listado de contactos
	 * 
	 * @param type $type
	 * @param type $query
	 * @param type $start
	 * @param type $limit
	 * @return type
	 */
	public function getAll($start = 0, $limit = 20)
	{
		//Parametros GET para obtener listado de contactos
		$params = "?start=$start&limit=$limit&metadata=true";
		
		//conexion al API
		$this->_client->setUri($this->_uri . $params);
		$response = $this->_client->request('GET');
		$_data = $response->getBody();
		$data = json_decode($_data, true);

		//Si ocurre un error...
		if (isset($data['code']) && $data['code'] !== 200)
		{
			return $data;
		}
		
		//curamos los datos
		$_contacts = $this->_parseData($data['data']);
		$contacts = array();
		
		//creamos array de contactos
		foreach ($_contacts as $c)
		{
			$contact = new Application_Model_Contact($c);
			array_push($contacts, $contact);
		}

		//retornamos array con metadatos ya listos para la respuesta
		return [
			'total' => $data['metadata']['total'],
			'data' => $contacts,
		];
	}

	/**
	 * Metodo para eliminar un contacto
	 * 
	 * @param type $id
	 * @return type
	 */
	public function delete($id)
	{
		//conexion al API
		$this->_client->setUri($this->_uri . "/$id");
		$response = $this->_client->request('DELETE');
		$_data = $response->getBody();
		$data = json_decode($_data, true);

		//Si ocurre un error...
		if (isset($data['code']) && $data['code'] !== 200)
		{
			return $data;
		}

		//retornamos la respuesta
		return $data;
	}

	/**
	 * Este metodo permite curar los datos para adecuarlos a 
	 * la estructura definida en esta aplicación
	 * Y modificar la estructura utilizada en el APi de Alegra
	 * 
	 * @param type $contactData
	 * @return boolean
	 */
	private function _parseData($contactData = [])
	{
		$i = 0;
		foreach ($contactData as $c)
		{
			$contactData[$i]['isClient'] = false;
			$contactData[$i]['isProvider'] = false;
			if (isset($c['address']['city']))
			{
				$contactData[$i]['city'] = [$c['address']['city']];
			}
			if (isset($c['priceList']['id']))
			{
				$contactData[$i]['priceList'] = [$c['priceList']['name']];
			}
			if (isset($c['seller']['id']))
			{
				$contactData[$i]['seller'] = [$c['seller']['name']];
			}
			if (isset($c['term']['id']))
			{
				$contactData[$i]['term'] = [$c['term']['name']];
			}
			if ((isset($c['type'][0]) && 'client' === $c['type'][0]) || 
					(isset($c['type'][1]) && 'client' === $c['type'][1]))
			{
				$contactData[$i]['isClient'] = true;
			}
			if ((isset($c['type'][0]) && 'provider' === $c['type'][0]) || 
					(isset($c['type'][1]) && 'provider' === $c['type'][1]))
			{
				$contactData[$i]['isProvider'] = true;
			}
			$i++;
		}
		return $contactData;
	}

}
