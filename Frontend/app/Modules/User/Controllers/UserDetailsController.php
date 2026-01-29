<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserDetailsController extends Controller
{
    protected $helper;
    protected $API_URL_3;

    public function __construct()
    {
        $this->API_URL_3 = env('API_HOST_V3');
        $this->helper = new helper();
    }

    public function employeeFullDetailsPage(Request $request)
    {
        try {
            $data['user_id'] = $request->query('id');
            $api_url = env('MAIN_API').'admin/employees/'.$data['user_id'];
            $method = 'get-with-token';
            $response = $this->helper->postApiCall($method, $api_url, []);
            $result['code'] = 200;
            $result['data'] = $response['data'];
            $result['msg'] = $response['message']; 
                 return view("User::EmployeeDetail.EmployeeFullDetails",
                 ["user_details" => $result]);
         } catch (\Exception $e) {
            return Redirect::back()->withErrors(['msg', 'No response or No User Found.']);
        }
    }

    public function getWebAppHistory(Request $request)
    {
         try {
            $session_user = '';
            if(Session::has('admin_session')) $session_user = Session::get('admin_session')['role'];
            else if(Session::has('employee_session')) $session_user = Session::get('employee_session')['role'];

            // Security: Changed from GET to POST to avoid sensitive data in query params
            $data = $request->input('data');
            parse_str($data, $parsedData);
            
            $api_url = env('MAIN_API').$session_user.'/web-app-activity';
            $method = 'post_with_token';
            $postData = array(
                "employeeId" => isset($parsedData['employeeId']) ? (int)$parsedData['employeeId'] : null,
                "startDate" => $parsedData['startDate'] ?? null,
                "endDate" => $parsedData['endDate'] ?? null,
                "type" => isset($parsedData['type']) ? (int)$parsedData['type'] : 1
            );
            $response = $this->helper->postApiCall($method, $api_url, $postData);
            
            // Handle response properly
            if (isset($response['data'])) {
                $result['code'] = $response['data']['code'] ?? 200;
                $result['data'] = $response['data']['data'] ?? $response['data'];
                $result['msg'] = $response['data']['message'] ?? 'Success';
            } else {
                $result['code'] = 500;
                $result['data'] = null;
                $result['msg'] = $response['message'] ?? 'Error occurred';
            }
            
            return $result;
        } catch (\Exception $e) {
             return $this->helper->errorHandler($e, ' UserDetailsController =>getBrowserHistory => Method-post ');
        }
    } 


    public function getTimeSheetData(Request $request)
    {
         try { 
            $data = $request->input('data');
            parse_str($data, $parsedData);
            $session_user = '';
            if(Session::has('admin_session')) $session_user = Session::get('admin_session')['role'];
            else if(Session::has('employee_session')) $session_user = Session::get('employee_session')['role'];
            $api_url = env('MAIN_API').$session_user.'/attendance';
            $method = "post_with_token";  
            $data = array(
                "start_date" => $parsedData['start_date'],
                "end_date" => $parsedData['end_date'], 
                "employee_id" => $parsedData['employee_id'], 
                "skip" => $parsedData['skip'], 
                "limit" => $parsedData['limit'], 
            );  
            $response = $this->helper->postApiCall($method, $api_url,$data);
            $result['code'] = $response['data']['code'];
            $result['data'] = $response['data']['data'];
            $result['msg'] = $response['data']['message'];
            return $result;
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' UserDetailsController => getTimeSheetData => Method-get ');
        }
    }
}
