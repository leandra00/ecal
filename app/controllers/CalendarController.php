<?php

/*
 * CalendarController
 * A CRUD for calendars
 */
class CalendarController extends \Phalcon\Mvc\Controller
{

    /*
     * indexAction
     */
    public function indexAction()
    {
        
        $calendars = Calendar::find(array('order' => 'name'));
        $this->view->setVar("calendars", $calendars);
    }
    
    /*
     * createAction
     */
    public function createAction()
    {
        
        $calendar = new Calendar();
        
        $calendar->name = $this->request->getPost("name", "striptags");
       
        if (!$calendar->create()) {
        
            foreach ($calendar->getMessages() as $message) {
                $this->flash->error((string) $message);
            }
            
        
        } else {
            $this->flash->success("Calendar was created successfully");
           
        }
        return $this->dispatcher->forward(
            array(
                'controller' => 'calendar',
                'action' => 'index')
        );
    }

    /*
     * updateAction
     */
    public function updateAction()
    {
        $id = $this->request->getPost("id", "int");
        
        $calendar = Calendar::findFirstById($id);
        if (!$calendar) {
            $this->flash->error("calendar does not exist " . $id);
            
        }

        $calendar->name = $this->request->getPost('name', 'striptags');
        if ($calendar->save()){
            $this->flash->success("Calendar was updated successfully");
        }
       
        return $this->dispatcher->forward(
            array(
                'controller' => 'calendar', 
                'action' => 'index')
            );
    }

    /*
     * removeAction
     * Delete calendar and appointments in it.
     * @todo show user a confirmation before deleteing (maybe use js).
     */
    public function removeAction()
    {
        $id = $this->request->get("id", "int");
        
        $calendar = Calendar::findFirstById($id);
        if (!$calendar) {
            $this->flash->error("calendar does not exist " . $id);
        
        }
        
        $appointments = Appointment::find(array(
            'conditions' => "calendar_id = $id"));        
        foreach ($appointments as $appointment) {
            $appointment->delete();
        }
        
        if ($calendar->delete()){
            $this->flash->success("Calendar was deleted successfully");
        }
    
        return $this->dispatcher->forward(
            array(
                'controller' => 'calendar',
                'action' => 'index')
        );
    }
    
}