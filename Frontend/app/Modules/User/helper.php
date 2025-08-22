<?php


namespace App\Modules\User;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Mockery\CountValidator\Exception;


class helper
{
    public function __construct()
    {
        $this->API_URL = env('API_URL');
        $this->client = new Client();
        $this->API_URL_3 = env('API_HOST_V3') . 'api/' . env('API_VERSION_3');

    }


    public function postApiCall($method, $api_url, $data, $is_multipart = false)
    {
        $role = $this->getHostName();
        $session_token = '';
        if(Session::has('admin_session')) $session_token = Session::get('admin_session')['token'];
        else if(Session::has('employee_session')) $session_token = Session::get('employee_session')['token'];
       
        switch (strtolower($method)) {
            case "get-with-token":
                $response = [];
                try {
                    $response = $this->client->get($api_url, [
                        'headers' => [
                            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer ' . $session_token
                        ]
                    ]); 
                    $response = json_decode($response->getBody()->getContents(), true); 
                    return $response;
                } catch (Exception $e) {
                    $response->code = 400;
                    $response->message = $e->getMessage();
                    Log::info('Exception postApiCall case-get' . $e->getLine() . "=> code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                    return $response;
                }
                break; 
            case "post":
                $result = [];
                $response = null; 
                try { 
                    $response = $this->client->request('POST', $api_url, [ 
                        'form_params' => $data,
                        'headers' => [
                            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Content-Type' => 'application/x-www-form-urlencoded',
                        ]
                    ]);
                    if ($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody()->getContents(), true);
                        $result['statusCode'] = $response->getStatusCode();
                        $result['data'] = $data;

                        return $result;
                    } else {
                        return (['status' => 'failed', 'code' => 500, 'message' => 'The Server is temporarily unable to service your request due to maintenance downtime. Please try later']);
                    }
                } catch (Exception $e) {
                    $response->code = 400;
                    $response->message = $e->getMessage();
                    Log::info('Exception postApiCall post' . $e->getLine() . "=> code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                    return $response;
                } 
                break;
            case "post_with_token":
                $result = [];
                $response = null; 
                try { 
                    $response = $this->client->request('POST', $api_url, [ 
                        'form_params' => $data,
                        'headers' => [
                            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer ' . $session_token
                            ]
                        ]);
                      if ($response->getStatusCode() == 201 || $response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody()->getContents(), true);
                        $result['statusCode'] = $response->getStatusCode();
                        $result['data'] = $data;

                        return $result;
                    } else {
                        return (['status' => 'failed', 'code' => 500, 'message' => 'The Server is temporarily unable to service your request due to maintenance downtime. Please try later']);
                    }
                } catch (Exception $e) {
                    $response->code = 400;
                    $response->message = $e->getMessage();
                    Log::info('Exception postApiCall post' . $e->getLine() . "=> code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                    return $response;
                } 
                break;


            //  FOR DELETE METHOD
            case "delete":
                $response = [];
                try {
                    $response = $this->client->delete($api_url, [
                        'form_params' => $data,
                        'headers' => [
                            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer ' . $session_token
                        ]
                    ]);

                    $response = json_decode($response->getBody()->getContents(), true);

                    return $response;
                } catch (Exception $e) {
                    $response->code = 400;
                    $response->message = $e->getMessage();
                    Log::info('Exception postApiCall delete' . $e->getLine() . "=> code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                    return $response;
                }
                break;

            //  FOR PUT METHOD
            case "put":
                $response = [];
                try {
                    $response = $this->client->put($api_url, [
                        'form_params' => $data,
                        'headers' => [
                            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer ' . $session_token
                        ]
                    ]);

                    $response = json_decode($response->getBody()->getContents(), true);

                    return $response;
                } catch (Exception $e) {
                    $response->code = 400;
                    $response->message = $e->getMessage();
                    Log::info('Exception postApiCall put' . $e->getLine() . "=> code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                    return $response;
                }
                break;
            case "post_localisation":
                $response = [];
                try {
                    $response=$this->client->request('POST',$api_url,[
                        'json'=>$data,
                         'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $session_token
                        ]
                         ]);
                    $response = json_decode($response->getBody()->getContents(), true);
                    return $response;
                } catch (Exception $e) {
                      $response->code = 400;
                    $response->message = $e->getMessage();
                    Log::info('Exception postApiCall put' . $e->getLine() . "=> code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                    return $response;
                }
            break;
            //  FOR PATCH METHOD
            case "patch":
                $response = [];
                try {
                    $response = $this->client->patch($api_url, [
                        'form_params' => $data,
                        'headers' => [
                            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer ' . Session::get($role)['token']['data']
                        ]
                    ]);

                    $response = json_decode($response->getBody()->getContents(), true);

                    return $response;
                } catch (Exception $e) {
                    $response->code = 400;
                    $response->message = $e->getMessage();
                    Log::info('Exception postApiCall patch' . $e->getLine() . "=> code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                    return $response;
                }
                break;

            default:
                $response = "please, send the parameters properly";
                Log::info('Exception postApiCall default');
                return $response;
        }
    }

  

    public function logException($method, $message)
    {
        Log::info('Exception in ' . $method . ' => ' . $message);
    }

    public function employeesList()
    {
        $api_url = env('MAIN_API'). 'admin/employees';
        $method = "get-with-token";
        $data['skip'] = "";
        $data['limit'] = "";
        try {
            $response = $this->postApiCall($method, $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->errorHandler($e, ' helperfile => employees => Method-post ');
        }
    }

//    Response handlers Added by Suresh
    public function responseHandler($response)
    {
        if ($response['data']['code'] === 200) {
            $result['code'] = 200;
            $result['data'] = $response['data']['data'];
            $result['msg'] = $response['data']['message'];
            if (isset($response['data']['totalCount'])) {
                $result['count'] = $response['data']['totalCount'];
            }
        } else if ($response['data']['code'] === 401) {
            $result['code'] = $response['data']['code'];
            $result['data'] = $response['data']['data'];
            $result['msg'] = $response['data']['message'];
        } else {
            $result['code'] = $response['data']['code'];
            $result['msg'] = $response['data']['code'] === 400 ? $response['data']['message'] : $result['msg'] = $response['data']['code'] == 404 ? $response['data']['error'] : $response['data']['message'];
        }
        return $result;
    }

    //    Response handlers Added by Lakshmi
    public function responseHandlerWithErrorMsg($response)
    {
        if ($response['data']['code'] == 200) {
            $result['code'] = 200;
            $result['data'] = $response['data']['data'];
            $result['msg'] = $response['data']['message'];
        } else {
            $result['code'] = $response['data']['code'];
            $result['data'] = $response['data']['data'];
            $result['msg'] = $response['data']['code'] == 400 ? $response['data']['message'] : $response['data']['error'];
        }
        return $result;
    }

    public function responseHandlerWithoutStatusCode($response)
    {
        if ($response['code'] === 200) {
            $result['code'] = 200;
            $result['data'] = $response['data'];
            $result['msg'] = $response['message'] ?? "Successfully fetched";
            if (isset($response['count'])) {
                $result['count'] = $response['count'];
            }
            if (isset($response['isCompleted'])) $result['isCompleted'] = $response['isCompleted'];
            if (isset($response['salaryStructure'])) $result['salaryStructure'] = $response['salaryStructure'];
            if (isset($response['orgSettings'])) $result['orgSettings'] = $response['orgSettings'];
            if (isset($response['payoutDate'])) $result['payoutDate'] = $response['payoutDate'];
            if (isset($response['project_details'])) $result['project_details'] = $response['project_details'];
        } else {
            $result['code'] = $response['code'];
            $result['data'] = $response['data'];
            $result['msg'] = $response['code'] === 400 ? $response['message'] : $response['error'];
        }
        return $result;
    }

    public function responseHandlerData($response)
    {
        if ($response['data']['code'] == 200) {
            $result['code'] = 200;
            $result['msg'] = $response['data']['message'];
            $result['data'] = $response['data'];
        } else if ($response['data']['code'] == 401) {
            $result['code'] = $response['data']['code'];
            $result['data'] = $response['data']['data'];
            $result['msg'] = $response['data']['message'];
        } else {
            $result['code'] = 400;
            $result['msg'] = $response['data']['code'] == 400 ? $response['data']['message'] : $response['data']['error'];
            $result['data'] = $response['data'];
        }
        return $result;
    }

    public function responseHandlerWithLoop($response)
    {
        if ($response['code'] == 200) {
            $result['code'] = 200;
            $result['data'] = $response['data'];
            $result['msg'] = $response['hasMoreData'];
        } else if ($response['code'] == 404) {
            $result['code'] = 200;
            $result['data'] = $response['data'];
            $result['msg'] = $response['hasMoreData'];
        } else {
            $result['code'] = $response['code'];
            $result['msg'] = $response['code'] == 400 ? $response['message'] : $response['error'];
        }
        return $result;
    }

    public function errorHandler($exception, $functionName)
    {
        Log::info('Exception ' . $exception->getLine() . " => Function Name => " . $functionName . " => code =>" . $exception->getCode() . " => message =>  " . $exception->getMessage());
        $result['code'] = 500;
//        $result['msg'] = "Something went wrong...";
        $result['msg'] = "Some error occurred while fetching data Please reload it...";
        $result['data'] = null;
        return $result;
    }

    public function guzzleErrorHandler($guzzleException, $functionName)
    {
        $response = $guzzleException->getResponse();
        $result['code'] = 403;
        $result['msg'] = $response->reasonPhrase();
        Log::info("GuzzleException => Function Name => " . $functionName . "=> code =>" . $result['code'] . " => message =>  " . $result['msg']);
        return $result;
    }

//    Uninastaller checking with special admins
    public function uninstallerCheckAdmins()
    {
        $specialAdmins = explode(',', env('UNINSTALLER_ADMINS'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }

    public function checkHost()
    {
        $amemberAllowedHost = [
            "app.dev.empmonitor.com",
            "app.empmonitor.com",
            "remitchoice.empmonitor.com"
        ];
        return in_array($_SERVER['HTTP_HOST'], $amemberAllowedHost);
    }

    public function checkPrivateHost()
    {
        $amemberAllowedHost = [
            "app.dev.empmonitor.com",
            "app.empmonitor.com"
        ];
        return in_array($_SERVER['HTTP_HOST'], $amemberAllowedHost);
    }

    public function checkAdminAndCalculatePercentage($data, $Total_hrs)
    {
        $adminId = Session::get($this->getHostName())['token']['organization_id'];
        return ($adminId == env('SPECIAL_ADMIN')) ? round(($data / 30600) / $Total_hrs * 100, 2) : round(($data / 3600) / $Total_hrs * 100, 2);
    }

    public function getRoleValue()
    {
        return Session::get('role');
    }

    public function checkOrganizationId()
    {
        $specialAdmins = explode(',', env('SPECIAL_ADMIN'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }
    public function checkOrgIdle()
    {
        $specialAdmins = explode(',', env('SPECIAL_IDLE_ADMIN'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }
    // For orange organzation hide the features
    public function checkOrangeClient()
    {
        $specialAdmins = explode(',', env('ORANGE_HIDE_FEATURES'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }


    #For TCS we are giving feature list collapse/ merge columns for same emp-code
    public function collapseMergeColumns()
    {
        $specialAdmins = explode(',', env('COLLAPSE_MERGE_EMP_DETAILS'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }

    // to disable the alerts and auto email report
    public function checkOrgDisable()
    {
        $specialAdmins = explode(',', env('ALERTS_EMAIL_DISABLE_ADMINS'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }

    public function checkResellerOrgId()
    {
        $ResellerAdmins = explode(',', env('RESELLER_ADMIN'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $ResellerAdmins);
    }

    //   To merge the duplicate column in the time sheets for the special admins only
    public function mergeColumns()
    {
        $specialAdmins = explode(',', env('MERGE_COLUMNS_TIMESHEETS'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }

    public function getSessionForROle($role)
    {
        switch ($role) {
            case 'admin':
                return Session::get(env('Admin'));
                break;
            case 'employee':
                return Session::get(env('Employee'));
                break;
            case 'manager':
                return Session::get(env('Manager'));
                break;
            case 'teamlead':
                return Session::get(env('Teamlead'));
                break;


        }
    }

    public function getHostName()
    {
        return explode('/', $_SERVER["REQUEST_URI"])[1];
    }

    public function getHrmsHostName()
    {
        return explode('/', $_SERVER["REQUEST_URI"])[1] . "/". explode('/', $_SERVER["REQUEST_URI"])[2] ;
    }

    public function getMyId()
    {
        if ($this->getHostName() === env('Admin')) {
            return Session::get($this->getHostName())['token']['my_self'];
        } else {
            return Session::get($this->getHostName())['token']['u_id'];
        }
    }

    public function getMyUserId()
    {
        return Session::get($this->getHostName())['token']['user_id'];
    }

    //    guzzle http request handler for catch block used as (\GuzzleHttp\Exception\RequestException $e) this exception
    public function GuzzleHTTPException($exception, $functionName)
    {
        Log::info('Exception ' . $exception->getLine() . " => Function Name =>'.$functionName. '  => code =>" . $exception->getCode() . " => message =>  " . $exception->getMessage());
        $parsedMessage = json_decode($exception->getResponse()->getBody()->getContents());
        $data['code'] = $parsedMessage->code;
        $data['msg'] = $parsedMessage->message;
        $data['data'] = null;
        $data['complete'] = $parsedMessage;
        return $data;
    }

    // users id present here will not be able to see my productivity ( can be extended to other modules in future)
    public function TceViewAdmins($id)
    {
        $arr = [9265, 9267, 7973, 7975];
        return in_array($id, $arr);
    }


    public function shiftById($id)
    {
        try {
            $api_url = $this->API_URL_3 . '/organization-shift?id=' . $id;
            $method = "get";
            $response = $this->postApiCall($method, $api_url, 0);
            if ($response['data'] != null) {
                foreach ($response['data'] as $key => $value) {
                    $response['data'][$key]['data'] = json_decode($value['data'], true);
                    foreach ($response['data'][$key]['data'] as $key1 => $value1) {
                        if ($value1['status'] == false) {

                            unset($response['data'][$key]['data'][$key1]);
                        }

                    }
                    if (isset($response['data'][$key]['color_code'])) {
                        if ($response['data'][$key]['color_code'] == 4) $response['data'][$key]['color_code'] = "primary_shift"; //blue
                        if ($response['data'][$key]['color_code'] == 1) $response['data'][$key]['color_code'] = "success_shift"; //green
                        if ($response['data'][$key]['color_code'] == 3) $response['data'][$key]['color_code'] = "danger_shift";//red
                        if ($response['data'][$key]['color_code'] == 5) $response['data'][$key]['color_code'] = "dark_shift"; //black
                        if ($response['data'][$key]['color_code'] == 2) $response['data'][$key]['color_code'] = "warning_shift";//yellow
                        if ($response['data'][$key]['color_code'] == 6) $response['data'][$key]['color_code'] = "info_shift";//Moderate blue

                    }

                }
            }
            $result['code'] = 200;

            $result['data'] = $response['data'];
        } catch (\GuzzleHttp\Exception\RequestException $e) {

            $returnedData = $this->GuzzleHTTPException($e, "helpert shiftById");
            $result['code'] = 204;
            $result['message'] = $returnedData['msg'];
            $result['data'] = null;

        } catch (\Exception $e) {

            $this->logException('helpert shiftById ', $e->getMessage());
            $result['code'] = 500;
            $result['message'] = "Something went wrong";
            $result['data'] = null;
        }
        return $result;
    }

    public function getLocationsDept()
    {

        $api_url = $this->API_URL_3 . '/location/get-locations';
        $method = "post";
        $data['skip'] = "";
        $data['limit'] = "";
        try {

            $response = $this->postApiCall($method, $api_url, $data);
            return $this->responseHandler($response);
        } catch (\Exception $e) {
            return $this->errorHandler($e, ' helper => getLocationsDept => Method-post ');
        }
    }

    public function getLocationsWithDept()
    {
        $api_url = $this->API_URL_3 . '/location/get-locations-dept';
        $method = "post";
        $data['skip'] = "";
        $data['limit'] = "";
        try {
            $response = $this->postApiCall($method, $api_url, $data);
            return $this->responseHandler($response);
        } catch (\Exception $e) {
            return $this->errorHandler($e, ' helper => getLocationsWithDept => Method-post ');
        }
    }


    public function getDepartmentsByLocationID($location)
    {
        $api_url = $this->API_URL_3 . '/location/get-department-by-location';
        $method = "post";
        if ($location == "All") $location = "";
        $data['location_id'] = $location;

        try {
            $response = $this->postApiCall($method, $api_url, $data);
            return $this->responseHandler($response);
        } catch (\Exception $e) {
            return $this->errorHandler($e, ' helper => getLocationsWithDept => Method-post ');
        }
    }

    public function responseHandlerWithResponse($response)
    {
        if ($response['code'] === 200) {
            $result['code'] = 200;
            $result['data'] = $response['data'];
            $result['response'] = $response;
            $result['msg'] = $response['message'] ?? "Successfully fetched";
        } else {
            $result['code'] = $response['code'];
            $result['data'] = $response['data'];
            $result['response'] = [];
            $result['msg'] = $response['code'] === 400 ? $response['message'] : $response['error'];
        }
        return $result;
    }

    //    Special admin with less features
    public function specialAdminWithLessFeatures()
    {
        $specialAdmins = explode(',', env('SPECIAL_ADMIN_WITH_LESS_FEATURES'));
        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdmins);
    }
    //special Admin With IPMaskFeature

    public function specialAdminWithIPMaskFeature()

    {

        $specialAdminsIP = explode(',', env('SPECIAL_ADMIN_WITH_IPMASK_FEATURE'));

        return in_array(Session::get($this->getHostName())['token']['organization_id'], $specialAdminsIP);

    }
    public function checkEnvPermission($envname)
    {
        return in_array(Session::get($this->getHostName())['token']['organization_id'], explode(',', env($envname)));
    }

    public function bulkWebSiteUploadCurl($api_url, $data, $type)
    {
        try {
            $curl = curl_init();
            $role = $this->getHostName();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $type,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . Session::get($role)['token']['data'],
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return (json_decode($response));
        } catch (\Exception $e) {
            $result['code'] = 405;
            $result['msg'] = "something went wrong";
            return $result;
        }
    }
    public function specialEmployeeWithLessFeatures()
    {
        $result = true;
        if($this->checkEnvPermission('EMPLOYEE_DASHBOARD_CUSTOM_FEATURE') && $this->getHostName() === env('Employee'))
        {
            $result = false;
        }
        return $result;
    }
    public function specialEmployeeWithCustomTimesheet()
    {
        $result = true;
        if($this->checkEnvPermission('DISABLE_TIME_CLAIM_EMPLOYEE_DASHBOARD') && $this->getHostName() === env('Employee'))
        {
            $result = false;
        }
        return $result;
    }
    public function getAllNonadmin()
    {
        try {
            $api_url = $this->API_URL_3 . '/user/get-non-admin';
            $method = "get";
            $data['skip'] = 0;
            $data['limit'] = "";
            $response = $this->postApiCall($method, $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            Log::info('Exception in helper getDepartment ' . $e->getMessage());

        }
    }

}
