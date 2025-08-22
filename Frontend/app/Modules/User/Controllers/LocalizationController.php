<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Modules\User\helper;
use File;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Guzzle\Http\Exception\ClientErrorResponseException;


class LocalizationController extends Controller
{

    protected $client;
    protected $API_URL;
    protected $helper;
    protected $location;
    protected $department;


    public function __construct()
    {
        $this->location = [];
        $this->department = [];
        $this->client = new Client();
        $this->API_URL = env('MAIN_API');
        $this->helper = new helper();
    }

    public function getLocalize(Request $request){
        $result['locale']="";
        $result['timezone']="";
        $api_url = $this->API_URL . 'admin/localization';
        $method = "get-with-token";
        $response = $this->helper->postApiCall($method, $api_url, 0);
        $result=[];
        if($response['code'] == 200){
         $result['locale']=$response['data']['lang'];
         $result['timezone']=$response['data']['time_zone'];
        }
        return view('User::Localization.localization',['result'=>$result]);
    }
    public function saveLocalize(Request $request){
        if(!isset($request->language) ) return $result;
        $result['code']=400;
        if(!isset($request->timezone)) return $result;
        try{
            $api_url = $this->API_URL . 'admin/localization' ;
            $method = "post_localisation";
            $data['timezone']=$request->timezone;
            $data['language']=$request->language;
            $response = $this->helper->postApiCall($method, $api_url, $data);
            if($response['message'] == "Localization data updated successfully" ){
                $result['code'] =200;
                Session::put('locale', $request->language);
                return $result;
            }else{
                $result['code']=500;
                $result['message']=$response['message'];
                return $result;
            }
        }catch (\GuzzleHttp\Exception\RequestException  $e) {
            return $this->helper->GuzzleHTTPException($e, "saveLocalize");
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' saveLocalize ');
        }

    }
}
