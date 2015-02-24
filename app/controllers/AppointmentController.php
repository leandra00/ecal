<?php

use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Logger\Formatter\Line as LineFormatter;

/*
 * AppointmentController
 * A CRUD for appointments
 */
class AppointmentController extends \Phalcon\Mvc\Controller
{
    
    /*
     * indexAction
     */
    public function indexAction()
    {
        
        $calendar_id = $this->request->get('calendar_id');
        if (!$calendar_id) {
            $calendar_id = $this->dispatcher->getParam('calendar_id');
        }
        $calendar = Calendar::findFirstById($calendar_id);
        
        if (!$calendar) {
            
            $this->flash->error('Calendar not found.');
            return $this->dispatcher->forward(
                array(
                    'controller' => 'calendar',
                    'action' => 'index')
            );
        }
        
        $appointments = Appointment::find(array(
            'conditions' => "calendar_id = $calendar_id", 
            'order' => 'start_date, start_time'));
             
        $this->view->setVar("calendar_name", $calendar->name);
        $this->view->setVar("calendar_id", $calendar_id);
        $this->view->setVar("appointments", $appointments);
    }
    
    /*
     * createAction
     * Log if an appointment is created
     * 
     * @todo validate start date and time are in the future and end date and 
     * time occur after start date/time
     */
    public function createAction()
    {
        
        $appointment = new Appointment();
        $appointment->calendar_id   = $this->request->getPost("calendar_id", "int");
        $appointment->title         = $this->request->getPost("title", "striptags");
        $appointment->location      = $this->request->getPost("location", "striptags");
        $appointment->start_date    = $this->request->getPost("start_date", "striptags");
        $appointment->start_time    = $this->request->getPost("start_time", "striptags");
        $appointment->end_date      = $this->request->getPost("end_date", "striptags");
        $appointment->end_time      = $this->request->getPost("end_time", "striptags");
        $appointment->notes         = $this->request->getPost("notes", "striptags");
       
        if (!$appointment->create()) {
        
            foreach ($appointment->getMessages() as $message) {
                $this->flash->error((string) $message);
            }
            
        
        } else {
            $this->log('create', $id);
            $this->flash->success("Appointment was created successfully");
           
        }
        return $this->dispatcher->forward(
            array(
                'controller' => 'appointment',
                'action' => 'index',
                'params' => array('calendar_id' => $appointment->calendar_id)
            )
        );
    }

    /*
     * updateAction
     * Log if appointment is updated successfully.
     * 
     * @todo validate start date and time are in the future and end date and 
     * time occur after start date/time
     */
    public function updateAction()
    {
        $id = $this->request->getPost("id", "int");
        
        $appointment = Appointment::findFirstById($id);
        if (!$appointment) {
            $this->flash->error("appointment does not exist " . $id);
            
        }

        $appointment->title         = $this->request->getPost('title', 'striptags');
        $appointment->location      = $this->request->getPost("location", "striptags");
        $appointment->start_date    = $this->request->getPost("start_date", "striptags");
        $appointment->start_time    = $this->request->getPost("start_time", "striptags");
        $appointment->end_date      = $this->request->getPost("end_date", "striptags");
        $appointment->end_time      = $this->request->getPost("end_time", "striptags");
        $appointment->notes         = $this->request->getPost("notes", "striptags");
        
        if ($appointment->save()){
            $this->log('update', $id);
            $this->flash->success("Appointment was updated successfully");
        }
        
        return $this->dispatcher->forward(
            array(
                'controller' => 'appointment', 
                'action' => 'index',
                'params' => array('calendar_id' => $appointment->calendar_id)
            )
         );
    }

    /*
     * removeAction
     * Log if appointment has been removed
     * 
     * @todo add confirmation message before deleting (maybe use js for this)
     */
    public function removeAction()
    {
        $id = $this->request->get("id", "int");
        
        $appointment = Appointment::findFirstById($id);
        if (!$appointment) {
            $this->flash->error("appointment does not exist " . $id);
        
        }
        
        if ($appointment->delete()){
            $this->log('remove', $id);
            $this->flash->success("Appointment was deleted successfully");
        }
    
        return $this->dispatcher->forward(
            array(
                'controller' => 'appointment',
                'action' => 'index',
                'params' => array('calendar_id' => $appointment->calendar_id)
            )
        );
    }
    
    /*
     * log
     */
    private function log($action, $id)
    {
        $logger = new FileAdapter("../logs/appointment.log");
        $formatter = new LineFormatter("%message%");
        $logger->setFormatter($formatter);
        
        $logger->log(date("Y-m-d H:i:s ") . strtoupper($action) . 
            " Appointment id $id has been " . strtolower($action) . 'd');
        
    }
}