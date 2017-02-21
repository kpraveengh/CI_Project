<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('common_helper11')) {

     function common_helper11() {
        $this->load->library('doctrine');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        return TRUE;
    }

}