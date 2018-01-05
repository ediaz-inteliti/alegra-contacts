<?php

class ContactsController extends Zend_Controller_Action
{
	public function init()
	{
		$this->getHelper('Layout')->disableLayout();
		$this->getHelper('ViewRenderer')->setNoRender();
		$this->getResponse()->setHeader('Content-Type', 'application/json');
	}

	public function indexAction()
	{
		//Get single contact
		if (($id = $this->_request->getQuery('id'))!= NULL)
		{
			$contacts = new Application_Model_ContactMapper();
			$data = $contacts->get($id);
			return $this->_helper->json->sendJson($data);
		}
		
		//Else, get contacts list
		$type = $this->_request->getQuery('type') ? $this->_request->getQuery('type') : '';
		$start = intval($this->_request->getQuery('start')) ? intval($this->_request->getQuery('start')) : 0;
		$limit = intval($this->_request->getQuery('limit')) ? intval($this->_request->getQuery('limit')) : 20;

		$contacts = new Application_Model_ContactMapper();
		$data = $contacts->getAll($type, '', $start, $limit);

		return $this->_helper->json->sendJson($data);
	}

	public function createAction()
	{
		$params = (array) json_decode($this->getRequest()->getPost('data'));
		unset($params['id']);

		$contact = new Application_Model_ContactMapper();
		$form = new Application_Model_Contact($params);
		$data = $contact->store($form);
		
		return $this->_helper->json->sendJson($data);
	}

	public function updateAction()
	{
		$params = (array) json_decode($this->getRequest()->getPost('data'));

		$contact = new Application_Model_ContactMapper();
		$form = new Application_Model_Contact($params);
		$data = $contact->store($form);
		
		return $this->_helper->json->sendJson($data);
	}

	public function deleteAction()
	{
		$param = json_decode($this->getRequest()->getPost('data'));

		$contact = new Application_Model_ContactMapper();
		if (count($param) > 1)
		{
			foreach ($param as $key => $value)
			{
				$data = $contact->delete($value->id);
			}
			
			if (isset($data['code']) && '200' == $data['code'])
			{
				return $this->_helper->json->sendJson([
							'code' => 200,
							'message' => 'Los contactos fueron eliminados correctamente.',
				]);
			}
		}
		$data = $contact->delete($param->id);
		
		return $this->_helper->json->sendJson($data);
	}

}
