<?php
/**
 * Controlador que recibe las peticiones del frontend y responde en formato JSON
 */
class ContactsController extends Zend_Controller_Action
{
	
	public function init()
	{
		//Setear el framework para que responda siempre en formato JSON y desactivar el uso de layouts y vistas
		$this->getHelper('Layout')->disableLayout();
		$this->getHelper('ViewRenderer')->setNoRender();
		$this->getResponse()->setHeader('Content-Type', 'application/json');
	}

	/**
	 * Metodo que recibe peciones para Lectura de contactos (Individual y lista)
	 * @return type
	 */
	public function indexAction()
	{
		//Obtener un contacto individual
		if (($id = $this->_request->getQuery('id'))!= NULL)
		{
			$contacts = new Application_Model_ContactMapper();
			$data = $contacts->get($id);
			
			//respuesta JSON
			return $this->_helper->json->sendJson($data);
		}
		
		//Obtener un listado de contactos
		$type = $this->_request->getQuery('type') ? $this->_request->getQuery('type') : '';
		$start = intval($this->_request->getQuery('start')) ? intval($this->_request->getQuery('start')) : 0;
		$limit = intval($this->_request->getQuery('limit')) ? intval($this->_request->getQuery('limit')) : 20;

		$contacts = new Application_Model_ContactMapper();
		$data = $contacts->getAll($type, '', $start, $limit);

		//respuesta JSON
		return $this->_helper->json->sendJson($data);
	}

	/**
	 * Metodo para la creación de contactos
	 * 
	 * @return type
	 */
	public function createAction()
	{
		$params = (array) json_decode($this->getRequest()->getPost('data'));
		unset($params['id']);

		$contact = new Application_Model_ContactMapper();
		$form = new Application_Model_Contact($params);
		$data = $contact->store($form);
		
		//respuesta JSON
		return $this->_helper->json->sendJson($data);
	}

	/**
	 * Metodo para la actualización de contactos
	 * 
	 * @return type
	 */
	public function updateAction()
	{
		$params = (array) json_decode($this->getRequest()->getPost('data'));

		$contact = new Application_Model_ContactMapper();
		$form = new Application_Model_Contact($params);
		$data = $contact->store($form);
		
		return $this->_helper->json->sendJson($data);
	}

	/**
	 * Metodo para la eliminación de contactos
	 * @return type
	 */
	public function deleteAction()
	{
		$param = json_decode($this->getRequest()->getPost('data'));

		$contact = new Application_Model_ContactMapper();
		
		//si es mas de un contactos
		if (count($param) > 1)
		{
			foreach ($param as $key => $value)
			{
				$data = $contact->delete($value->id);
			}
			
			if (isset($data['code']) && '200' == $data['code'])
			{
				//respuesta JSON
				return $this->_helper->json->sendJson([
							'code' => 200,
							'message' => 'Los contactos fueron eliminados correctamente.',
				]);
			}
		}
		
		//si es solo 1 contactos
		$data = $contact->delete($param->id);
		
		//repuesta JSON
		return $this->_helper->json->sendJson($data);
	}

}
