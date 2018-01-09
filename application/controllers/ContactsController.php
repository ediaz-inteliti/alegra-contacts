<?php
/**
 * Controlador que recibe las peticiones del frontend y responde en formato JSON
 */
class ContactsController extends Zend_Controller_Action
{
	/**
	 * Inicializacion de clase controller
	 * Instrucciones compartidas entre todos
	 *  los metodos de la clase, 
	 * se ejecuta antes de cualquier accion
	 */
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
		$contactsApi = new Application_Model_ContactApiMapper();
		
		//Obtener un contacto individual
		if (($id = $this->_request->getQuery('id'))!= NULL)
		{
			
			$data = $contactsApi->get($id);
			var_dump($data);
			//respuesta JSON
			return $this->_helper->json->sendJson($data);
		}
		
		//Obtener un listado de contactos
		$start = intval($this->_request->getQuery('start')) ? intval($this->_request->getQuery('start')) : 0;
		$limit = intval($this->_request->getQuery('limit')) ? intval($this->_request->getQuery('limit')) : 20;
		
		$data = $contactsApi->getAll($start, $limit);
		
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
		$_data = (array) json_decode($this->getRequest()->getPost('data'));
		unset($_data['id']);

		$contactApi = new Application_Model_ContactApiMapper();
		$data = new Application_Model_Contact($_data);
		$contact = $contactApi->store($data);
		
		//respuesta JSON
		return $this->_helper->json->sendJson($contact);
	}

	/**
	 * Metodo para la actualización de contactos
	 * 
	 * @return type
	 */
	public function updateAction()
	{
		$_data = (array) json_decode($this->getRequest()->getPost('data'));

		$contactApi = new Application_Model_ContactApiMapper();
		$data = new Application_Model_Contact($_data);
		$contact = $contactApi->store($data);
		
		return $this->_helper->json->sendJson($contact);
	}

	/**
	 * Metodo para la eliminación de contactos
	 * @return type
	 */
	public function deleteAction()
	{
		$_data = json_decode($this->getRequest()->getPost('data'));

		$contactApi = new Application_Model_ContactApiMapper();
		
		//si es mas de un contacto
		if (count($_data) > 1)
		{
			foreach ($_data as $c)
			{
				$data = $contactApi->delete($c->id);
			}
			
			if (isset($data['code']) && '200' == $data['code'])
			{
				//respuesta JSON
				return $this->_helper->json->sendJson([
							'code' => 200,
							'message' => 'Los contactos fueron borrados exitosamente.',
				]);
			}
		}
		
		//si es solo 1 contactos
		$data = $contactApi->delete($_data->id);
		
		//repuesta JSON
		return $this->_helper->json->sendJson($data);
	}

}
