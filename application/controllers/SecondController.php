<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  class SecondController extends CI_Controller {

    public function index(){
      $data['header']   = 'tampil/header';
      $data['side']     = 'tampil/side';
      $data['navbar']   = 'tampil/navbar';
      $data['content']  = 'tampil/tampiladmin';
      $data['footer']   = 'tampil/footer';
      $this->load->view('tampil/main',$data);
    }

    public function show()
    {
      $data['header']   = 'tampil/header';
      $data['side']     = 'tampil/side';
      $data['navbar']   = 'tampil/navbar';
      $data['content']  = 'tampil/tampiladmin';
      $data['footer']   = 'tampil/footer';
      $this->load->view('muncul/main',$data);
    }




  }
