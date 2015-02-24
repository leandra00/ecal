<?php

use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Logger\Formatter\Line as LineFormatter;

class AppointmentController extends \Phalcon\Mvc\Controller
{
    
    public function indexAction()
    {
        $calendar_id = $this->request->get('calendar_id');
        
        $appointments = Appointment::find(array(
            'calendar_id' => $calendar_id, 
            'order' => 'start_date, start_time'));
        
        $this->view->setVar("calendar_id", $calendar_id);
        $this->view->setVar("appointments", $appointments);
    }
    
    public function createAction()
    {
        
        $appointment = new Appointment();
        
        $appointment->title         = $this->request->getPost("title", "striptags");
        $appointment->location      = $this->request->getPost("location", "striptags");
        $appointment->start_date    = $this->request->getPost("start_date", "striptags");
        $appointment->start_time    = $this->request->getPost("start_time", "striptags");
        $appointment->end_date      = $this->request->getPost("end_date", "striptags");
        $appointment->end_time      = $this->request->getPost("end_time", "striptags");
        $appointment->notes         = $this->request->getPost("notes", "striptags");
       
        if (!$appointment->create()) {
        
            //The store failed, the following messages were produced
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
                'action' => 'index')
        );
    }

    
    public function updateAction()
    {
        $id = $this->request->getPost("id", "int");
        
        $appointment = Appointment::findFirstById($id);
        if (!$appointment) {
            $this->flash->error("appointment does not exist " . $id);
            
        }

        $appointment->title = $this->request->getPost('title', 'striptags');
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
                'action' => 'index')
            );
    }

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
                'action' => 'index')
        );
    }
    
    private function log($action, $id)
    {
        $logger = new FileAdapter("../app/logs/appointment.log");
        $formatter = new LineFormatter("%message%");
        $logger->setFormatter($formatter);
        
        $logger->log(date("Y-m-d H:i:s ") . strtoupper($action) . 
            " Appointment id $id has been " . strtolower($action) . 'd');
        
    }
}