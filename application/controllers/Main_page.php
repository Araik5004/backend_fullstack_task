<?php

use Model\Login_model;
use Model\Post_model;
use Model\User_model;
use Model\Comment_model;
use Model\Boosterpack_model;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');

        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts =  Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id){ // or can be $this->input->post('news_id') , but better for GET REQUEST USE THIS

        $post_id = intval($post_id);

        if (empty($post_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function comment(){ // or can be App::get_ci()->input->post('news_id') , but better for GET REQUEST USE THIS ( tests )



        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }


        $assign_id = App::get_ci()->input->post('assign_id');
        $text = App::get_ci()->input->post('text');
        $parent_id = App::get_ci()->input->post('parent_id');
        $parent_id = intval($parent_id);//if not 0 , then repost
        //$captcha_response = App::get_ci()->input->post('g-recaptcha-response');
        //$captcha_response = (string)$captcha_response;

        $data_validation = [
            'assign_id' => $assign_id,
            'text' => $text,
            //'captcha_response' => $captcha_response
        ];

        //set validation rules
        $this->form_validation->set_data($data_validation);
        $this->form_validation->set_rules('assign_id', 'assign_id', 'trim|required');
        //$this->form_validation->set_rules('captcha_response', 'captcha_response', 'trim|required');
        $this->form_validation->set_rules('text', 'text', 'trim|required|htmlspecialchars');
        if ($this->form_validation->run() == FALSE) {
            // if not valid load comments
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }


        //Check google recaptcha
        /*
        $keySecret = 'google_secret_key';
        $check = array(
            'secret'		=>	$keySecret,
            'response'		=>	$captcha_response
        );
        $startProcess = curl_init();
        curl_setopt($startProcess, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($startProcess, CURLOPT_POST, true);
        curl_setopt($startProcess, CURLOPT_POSTFIELDS, http_build_query($check));
        curl_setopt($startProcess, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($startProcess, CURLOPT_RETURNTRANSFER, true);
        $receiveData = curl_exec($startProcess);
        $finalResponse = json_decode($receiveData, true);
        */
        //end

        $assign_id = intval($assign_id);

        if (empty($assign_id) || empty($text)
            //|| !isset($finalResponse['success'])
        ){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($assign_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        // Todo: 2 nd task Comment
        $user = User_model::get_user();
        $data = [];
        $data['assign_id'] = $assign_id;
        $data['text'] = $text;
        $data['parent_id'] = $parent_id;
        $data['user_id'] = $user->get_id();
        $data['type'] = 'post';
        $data['public'] = 1;
        try
        {
            $comment = Comment_model::create($data);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }

    public function login()//$user_id
    {
        // Right now for tests we use from contriller
        $login = App::get_ci()->input->post('login');
        $password = App::get_ci()->input->post('password');

        $data = [
            'login' => $login,
            'password' => $password
        ];

        //set validation rules
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('login', 'login', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'password', 'trim|required|htmlspecialchars');
        if ($this->form_validation->run() == FALSE) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        // But data from modal window sent by POST request.  App::get_ci()->input...  to get it.


        $user_id = User_model::get_user_by_email($login);
        if(!$user_id || $user_id->get_password() !== $password)
        {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }
        Login_model::start_session($user_id);

        return $this->response_success(['user' => $user_id]);
    }


    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    public function add_money(){

        $sum = App::get_ci()->input->post('sum');

        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $sum = floatval($sum);

        if (!($sum > 0)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $user = User_model::get_user();

            $new_balance = User_model::add_balance($user , $sum);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        if(!$new_balance)
        {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        // todo: 4th task  add money to user logic
        return $this->response_success(['amount' => $sum]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

    public function buy_boosterpack(){

        $id = App::get_ci()->input->post('id');

        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $id = intval($id);

        if (!$id){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            //get current user
            $user = User_model::get_user();
            $booster_pack = new Boosterpack_model($id);

            //check user balance ,
            if($user->get_wallet_balance() < $booster_pack->get_price())
            {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_UNAVAILABLE);
            }

            $likes = User_model::user_buy_booster_pack($user , $booster_pack);


        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        if(!$likes)
        {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        // todo: 5th task add money to user logic
        return $this->response_success(['amount' => $likes]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }


    public function like(){

        $post_id = App::get_ci()->input->get('post_id');
        $post_id = intval($post_id);

        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        if (empty($post_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        //Check if user have likes  for liking this post
        $user = User_model::get_user();
        if($user->get_likes() <= 0)
        {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_UNAVAILABLE);
        }

        try
        {
            $post = new Post_model($post_id);

            $likes = Post_model::add_like_to_post($post , $user);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        if(!$likes)
        {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        // todo: 3rd task add like post\comment logic
        return $this->response_success(['likes' => $likes ]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

}
