<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Modules\User\helper;
use File;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    protected $client;
    protected $API_URL;
    protected $helper;
    protected $VERSION;
    protected $API_URL_3;
    protected $VERSION_3;

    public function __construct()
    {
        $this->client = new Client();
        $this->API_URL = env('API_HOST');
        $this->helper = new helper();
        $this->VERSION = env('API_VERSION');
        $this->API_URL_3 = env('API_HOST_V3');
        $this->VERSION_3 = env('API_VERSION_3');
        $this->user = $this->helper->getHostName();
    }

  
    public function loginpageWhitelabel(Request $request, $username = null, $password = null)
    {
        try {  
            $username = base64_decode($username);
            $password = base64_decode($password);
            if (isset($username) && isset($password)) { 
                $api_url = env('MAIN_API').'admin/login';
                $method = "post";  
                $amemberData = array(
                    "email" => $username,
                    "password" => $password, 
                ); 
               
                $adminAuthResponse = $this->helper->postApiCall($method, $api_url, $amemberData);
            
                $admin = array( 
                        'token' => $adminAuthResponse['data']['token'],
                        'id' => $adminAuthResponse['data']['id'],
                        'name' => $adminAuthResponse['data']['first_name'],
                        'email' => $adminAuthResponse['data']['email'],
                        // 'employee_code' => $adminAuthResponse['data']['employee_code'],
                        // 'time_zone' => $adminAuthResponse['data']['time_zone'],
                        'role' => $adminAuthResponse['data']['role'],
                        'photo_path' => '../' . env('DEFAULT_IMAGE'), 
                    );
                    Session::put('admin_session', $admin);
                    return redirect('admin/employee-details');
                    } else {
                         return redirect('admin-login')->with('error', 'Invalid authentication');
                    }  
        } catch (\Exception $e) {
            dd($e);
            Log::info('Exception ' . $e->getLine() . " => Function Name => loginpageWhitelabel => code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
            return redirect('admin-login')->with('error', 'Something went wrong. Please contact support Team');
        }
    }

    

    //curl-execution
    public function curl_execution($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        return $result;
    }
 
    //forgot-password
    public function forgotPassword(Request $request)
    {
        $result = [];
        $data = $request->all();
        try {
            $api_url = $this->API_URL_3 . 'api/' . $this->VERSION_3 . '/password/forgot-password';

            $method = "post";
            $response = $this->helper->postApiCall($method, $api_url, $data);
            if ($response['data']['code'] == 200) {
                $result['code'] = 200;
                $result['data'] = $response['data']['data'];
                $result['msg'] = $response['data']['message'];
            } else {
                $result['code'] = $response['data']['code'];
                $result['msg'] = $response['data']['code'] == 400 ? $response['data']['message'] : $response['data']['error'];
            }
            return $result;
        } catch (\Exception $e) {
            Log::info('Exception ' . $e->getLine() . " => Function Name => forgotPassword => code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
            $result['code'] = 500;
            $result['msg'] = "Something went wrong...";
            return $result;
        }
    }

    //on getting the email & token
    public function resetPage(Request $request)
    {
        if (Session::has('admin')) return redirect('/dashboard');
        else {
            $email = $request->email;
            $token = $request->token;
            return view("User::adminLogin")->with('reset', $request->all());
        }
    }

    //login
    public function login(Request $request)
    {
         if ($request->isMethod('get')) { 
         return view("User::adminLogin")->with('reset', []);
        } else if ($request->isMethod('post')) {
            $rules = array(
                "email" => 'required | email',
                "password" => 'required'
            );
            try {
                 $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                } else {
                    $method = "post";
                    $loginData = array(
                        "email" => $request->email,
                        "password" => $request->password,
                      );
 
                      $api_url = env('MAIN_API').'employee/login';
                     try {
                        $response = $this->client->post($api_url, [
                            'form_params' => $loginData,
                            'headers' => [
                                'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                                'Content-Type' => 'application/x-www-form-urlencoded',
                            ]
                        ]);
                    } catch (\GuzzleHttp\Exception\RequestException $e) {  
                        Log::info('Exception ' . $e->getLine() . " => Function Name =>Login  => code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                        $parsedMessage = json_decode($e->getResponse()->getBody()->getContents());
                        return redirect('login')->with('error', $parsedMessage->message);
                    }
                    if ($response->getStatusCode() == 200) { 
                        $response = json_decode($response->getBody()->getContents(), true);
                        // Session::put('locale', $response['language']);
                       if ($response['role'] == 'employee') {  
                           Session::put('employee_session', $response);
                             return redirect('/employee/myTimeline?id='.$response['id']);
                        } else {
                            return redirect('login')->with('error', $response['error']);
                        }

                    }
                }
            } catch (\Exception $e) {
                Log::info('Exception ' . $e->getLine() . " => Function Name =>Login  => code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                return redirect('login')->with('error', $e->getMessage());
            }
        }
    }
     
    //reset password
    public function resetPassword(Request $request)
    {
        $result = [];
        $data = $request->all();
        try {
            $rules = array(
                "new_password" => 'required|max:20|min:8',
                "confirm_password" => 'required_with:passwd|same:new_password',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $validator->errors();
            } else {
                $api_url = $this->API_URL_3 . 'api/' . $this->VERSION_3 . '/password/reset-password';

                $method = "put";
                $response = $this->helper->postApiCall($method, $api_url, $data);
                if ($response['code'] == 200) {
                    $result['code'] = 200;
                    $result['data'] = $response['data'];
                    $result['msg'] = $response['message'];
                } else {
                    $result['code'] = $response['code'];
                    $result['msg'] = $response['code'] == 400 ? $response['message'] : $response['error'];
                }
                return $result;
            }
        } catch (\Exception $e) {
            Log::info('Exception ' . $e->getLine() . " => Function Name => resetPassword => code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
            $result['code'] = 500;
            $result['msg'] = "Something went wrong...";
            return $result;
        }
    }
 
    //employee registration
    public function EmployeeRegistration(Request $request)
    {
        $rules = array(
            "name" => 'required|max:32|min:2|regex:/([a-zA-Zء-ي]+)([0-9٠-٩]*)/',
            "Full_Name" => 'required|regex:/([a-zA-Zء-ي]+)/',
            "email" => 'required|email|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,10})+$/',
            // "password" => 'required |regex:/^(?=.*\d)(?=.*[!-\/:-@\[-`{-~]).{8,}$/',
            // "confirmPassword" => 'required_with:passwd|same:password',
            "empCode" => 'required|', 
            "TimeZoneOffset" => 'required',
        );

        try {
            $customMessage = [
                'name.max' => __('messages.firstName_length'),
                // 'passwd.regex' => __('messages.Password_formate'),
                // 'c_passwd.required_with' => __('messages.confirm_password'),
                // 'c_passwd.same' => __('messages.Password_missmatch'),
                'username.regex' => __('messages.Username_alphanumeric'),
                'first_name.regex' => __('messages.special_characters'),
            ];
            $validator = Validator::make($request->all(), $rules, $customMessage);
            if ($validator->fails()) {
                return $validator->errors();
            } else { 
                $api_url = env('MAIN_API').'admin/register';
                $method = 'post_with_token';
                $data['firstName'] = $request->input('name');
                $data['lastName'] = $request->input('Full_Name');
                $data['email'] = strtolower($request->input('email'));
                $data['password'] = $request->input('password');
                $data['employeeCode'] = $request->input('empCode'); 
                $data['mobileNumber'] = $request->input('number'); 
                $data['timeZone'] = $request->input("TimeZoneOffset"); 
               
                $response = $this->helper->postApiCall($method, $api_url, $data); 
                if ($response['statusCode'] == 201) {
                    $result['code'] = 200;
                    $result['msg'] = 'success';
                    $result['data'] = ($response['data']); 
                    return $result;
                } else {
                    return $this->helper->responseHandler($response);
                }
            }
        } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => EmployeeRegistration => Method-post');
        }
    }


    /**
     * logout
     * * This is used to logout application and clearing the session and cookies
     * @param mixed $request
     * @return void
     */
    public function logout(Request $request)
    { 
        $this->destroyAuthToken(); 
        Session::forget($this->helper->getHostName()); 
        Session::forget('admin_session'); 
        return redirect('admin-login'); 
    }

 
    public function destroyAuthToken()
    {
        try {
//            To make the Token expire once User gets logout the site
            $api_url = $this->API_URL_3 . 'api/' . $this->VERSION_3 . '/auth/logout';
            $this->helper->postApiCall("get", $api_url, 0);
        } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => logout => Method-post');
        }
    }

    public function logouut()
    {
        if (Session::has('admin')) Session::forget('admin');
        if (Session::has('plan')) Session::forget('plan');
        return redirect('/');
    }
 

    

    public function error()
    {
        return redirect()->back()->with('alert', 'Sorry, already existed');
    }

   
    public function EmpDetails(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('User::EmployeeDetail.employeDetail', [ 
            ]);
        }
    }
   
    public function EmployeeDetails(Request $request)
    {
        $data['limit'] = count($request->all()) != 0 ? $request->input('showEntries') : 10;
        $data['skip'] = count($request->all()) != 0 ? $request->input('skipvalue') : 0;
        $data['name'] = count($request->all()) != 0 ? ($request->input('searchText') != null ? $request->input('searchText') : "") : "";
        
        $api_url = env('MAIN_API').'admin/employees?skip='.$data['skip']."&limit=".$data['limit']."&name=".$data['name'];
        $method = "get-with-token"; 
        if ($request->input('sortName') != '' && count($request->all()) != 0) {
            $data['sortColumn'] = $request->input('sortName');
            $data['sortOrder'] = $request->input('sortOrder');
        }
        try {
            $response = $this->helper->postApiCall($method, $api_url,[]);
             if ($response) {
                $result['code'] = 200;
                $result['msg'] = 'success'; 
                $result['data'] = $response;
                return $result;
            }
        } catch (\Exception $e) {
             return $this->ExceptionErrorHandler($e, 201, ' UserController => EmployeeDetails => Method-post');
        }
    }


    function multi_unique($src)
    {
        $output = array_map(
            "unserialize",
            array_unique(array_map("serialize", $src))
        );
        return $output;
    }
   

     public function editEmployee(Request $request)
    {
   
        $data['firstName']=$request->input('name');
        $data['lastName']=$request->input('Full_name');
        $data['email']=$request->input('email');
        $data['password']=$request->input('password');
        $data['mobileNumber']=$request->input('number');
        $data['timeZone']=$request->input('timeZone');
        $data['employeeCode']=$request->input('EmpCode');
        $data['employeeId']=(int) $request->input('hideId');
        $data['employeeRole']="employee";
      
        $method = "put";
        $api_url = env('MAIN_API') . 'admin/update-employee';
        try {
            $response = $this->helper->postApiCall($method, $api_url, $data);
           return $response;
            }
         catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "404", ' UserController => editEmployee => Method-delete');
        }
    }


    //  ************* deleting Single employee using delete icon *************
    public function Employeedelete(Request $request)
    {
        $api_url =  env('MAIN_API').'admin/employee-delete-multiple';
        $method = 'delete';
        $data['user_ids'] = [$request->input('DetedId')];
        if(Session::has('leftOverLicenses')) Session::forget('leftOverLicenses');
        try {
            $response = $this->helper->postApiCall($method, $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, 400, ' UserController => Employeedelete => Method-delete');
        }
    }
 


    public function logoutwhite(Request $request)
    {
        Session::forget('admin');
        if (Session::has('plan')) {
            Session::forget('plan');
            Session::flush();
            return redirect("admin-login");
        } else {
            Session::flush();
            return redirect('login');
        }
    }
 
    public function loginEmployee(Request $request)
    {
        if ($request->isMethod('get')) {
            if (Session::has('employee')) {
                return redirect(env('Employee') . '/myTimeline?id=' . Session::get(env('Employee'))['token']['user_id']);
            }
            return view("User::employeeLogin")->with('reset', []);
        } else if ($request->isMethod('post')) {
            $rules = array(
                "email" => 'required | email',
                "password" => 'required'
            );
            try {
                //validate login-creds
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                } else {
                    $loginData = array(
                        "userName" => $request->email,
                        "password" => $request->password,
                        "ip" => $request->ip
                    );

                    $api_url = $this->API_URL . 'api/v1/emp/auth';
                    $response = $this->client->post($api_url, [
                        'form_params' => $loginData,
                        'headers' => [
                            'user-agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Content-Type' => 'application/x-www-form-urlencoded',
                        ]
                    ]);

                    if ($response->getStatusCode() == 200) {
                        $response = json_decode($response->getBody()->getContents(), true);
                        if ($response['code'] == 200) {
                            if ($response['photo_path'] == 'undefined') $response['photo_path'] = env('DEFAULT_IMAGE');
                            elseif (substr($response['photo_path'], 0, 5) === "https") $response['photo_path'] = $response['photo_path'];
                            else $response['photo_path'] = env('API_HOST') . $response['photo_path'];

                            $employee = array(
                                'token' => $response
                            );
                            Session::put(env('Employee'), $employee);

                            return redirect(env('Employee') . '/myTimeline?id=' . Session::get(env('Employee'))['token']['user_id']);
                            return view('User::Employee.NewEmployeeDashboard');
                        } else if ($response['code'] == 400) {
                            return redirect('employee-login')->with('error', $response['message']);
                        } else {
                            return redirect('employee-login')->with('error', $response['error']);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::info('Exception ' . $e->getLine() . " => Function Name => loginEmployee => code =>" . $e->getCode() . " => message =>  " . $e->getMessage());
                return redirect('employee-login')->with('error', $e->getMessage());
            }
        }
    }
 
     public function getUserTrackDetails(Request $request, $id)
    {
        $api = $this->API_URL_3 . 'api/' . $this->VERSION_3 . '/settings/get-emp-setting-trac';
        $method = "post";
        $data['employee_id'] = $id;
        try {
            $response = $this->helper->postApiCall($method, $api, $data);
            if ($response['data']['code'] == 200) {
                if (!(isset($response['data']['data']['custom_tracking_rule']['tracking']['domain']))) {
                    $response['data']['data']['custom_tracking_rule']['tracking']['domain']['suspendKeystrokesPasswords'] = "true";
                }
            }
            $result['code'] = $response['data']['code'];
            $result['msg'] = $response['data']['message'];
            $result['data'] = $response['data']['data'];
        } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, 205, ' UserController => TrackUserSetting => Method-post');
        }
        return ($result);
    }

 

    public function ExceptionErrorHandler($exception, $code, $functionName)
    {
        Log::info('Exception ' . $exception->getLine() . " => Function Name => " . $functionName . " => code =>" . $exception->getCode() . " => message =>  " . $exception->getMessage());
        $result['code'] = $code;
        $result['msg'] = "Please, Reload and try again...";
        $result['ErrorMessage'] = $exception->getMessage();
        return $result;
    }

    public function guzzleErrorHandler($guzzleException, $functionName)
    {
        $response = $guzzleException->getResponse();
        $result['code'] = 403;
        $result['msg'] = $response->reasonPhrase;
        Log::info("GuzzleException => Function Name => " . $functionName . "=> code =>" . $result['code'] . " => message =>  " . $result['msg']);
        return $result;
    }
 

    public function permissionDenied()
    {
        return view('User::permissionDenied');
    }
 
    public function show_details(Request $request)
    {
        $api_url = env('MAIN_API').'admin/employees/'.$request->userId;
        $method = 'get-with-token';
        try {
            $response = $this->helper->postApiCall($method, $api_url, []);
            $result['code'] = 200;
            $result['data'] = $response['data'];
            $result['msg'] = $response['message'];
            return $result;
        } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => show_details => Method-post');
        }
    }
    
    public function DeleteMultiple(Request $request)
    {
        $data['user_ids'] = collect(explode(',', $request->input('user_ids')))->map(fn($id) => (int) $id)->filter(fn($id) => $id > 0)->values()->toArray();
        $method = "delete";
        $api_url = env('MAIN_API') . 'admin/employee-delete-multiple';
        try {
            $response = $this->helper->postApiCall($method, $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "500", ' UserController => DeleteMultiple => Method-delete');
        }
    }
    public function adminLogin(Request $request)
    {
        //         if not exists copy else no
        $logoPath = 'assets/images/logos/' . md5($_SERVER['HTTP_HOST']) . '.png';
        if (!(\Illuminate\Support\Facades\File::exists(public_path($logoPath)))) {
            Storage::disk('logos')->copy('assets/images/logos/EmpLogo.png', $logoPath);
        }
        $faviconPath = 'assets/images/favicons/' . md5($_SERVER['HTTP_HOST']) . '.png';
        if (!(\Illuminate\Support\Facades\File::exists(public_path($faviconPath)))) {
            Storage::disk('logos')->copy('assets/images/favicons/EmpFavicon.png', $faviconPath);
        }

        if ($request->isMethod('get')) {

            if (Session::has('admin')) {
                return redirect('admin/dashboard');
            }
            return view('User::loginWhitelabel');
        } else if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'password' => 'required|max:255',
                'username' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect('admin-login')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                $username = base64_encode($request->username);
                $password = base64_encode($request->password);
                $isClient = $request->isClient;
                if ($isClient === "true") {
                    return redirect('/resellerClientLogin/' . $username . '/' . $password . '/' . $isClient);
                } else {
                    return redirect('/loginpageWhitelabel/' . $username . '/' . $password);
                }
            }
        }
    }
    public function manageLocations()
    {
        $location = $this->getLocationsDept();
        $departments = $this->getDepartments();

        if (isset($location['data']['data'][0]['timezone'])) {
            for ($i = 0; $i < sizeof($location['data']['data']); $i++) {
                $location['data']['data'][$i]['timezone'] = str_replace("/", "", $location['data']['data'][$i]['timezone']);
            }
        }
        return view("User::manageLocations")->with(array('location_departmnet' => $location, 'departments' => $departments));
    }
     public function getLocationsDept()
    {
        try {
            $api_url = env('MAIN_API') . 'admin/locations';
            $method = "get-with-token";
            $response = $this->helper->postApiCall($method, $api_url, []);
            return $response;
             } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }
     public function addLoctation(Request $request)
    {
        try {
            $data['locationName'] = $request->input('location_name');
            $api_url = env('MAIN_API') . 'admin/locations';
            $method = "post_with_token";
            $response = $this->helper->postApiCall($method, $api_url, $data);
            return $response;
             } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }
     public function deleteLocation(Request $request)
    {
        try {
            $api_url = env('MAIN_API') . 'admin/locations/'.(int)$request->input('id');
            $method = "delete";
            $response = $this->helper->postApiCall($method, $api_url, []);
            return $response;
             } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }
     public function updateLocation(Request $request)
    {
        try {
            $data['locationName'] = $request->input('locationName');
            $api_url = env('MAIN_API') . 'admin/locations/'.(int)$request->input('id');
            $method = "put";
            $response = $this->helper->postApiCall($method, $api_url,$data);
            return $response;
             } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }
    public function manageDepartment()
    {
          $location = $this->getLocationsDept();
        $departments = $this->getDepartments();
        return view("User::manageDepartment")->with(array('departments' => $departments,'location_departmnet' => $location,));
    }

  public function getDepartments()
    {
        try {
            $api_url = env('MAIN_API').'admin/get-departments';
            $method = "get-with-token";
            $response = $this->helper->postApiCall($method, $api_url, []);
            return $response;
            } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }
      public function addDepartment(Request $request)
    {
        try {
            $data['departmentName'] = $request->input('departmentName');
            $data['locationId'] =(int)$request->input('locationId');
            $api_url = env('MAIN_API') . 'admin/add-department';
            $method = "post_with_token";
            $response = $this->helper->postApiCall($method, $api_url, $data);
            return $response;
             } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }
     public function deleteDepartment(Request $request)
    {
        try {
            $api_url = env('MAIN_API') . 'admin/delete-department/'.(int)$request->input('id');
            $method = "delete";
            $response = $this->helper->postApiCall($method, $api_url, []);
            return $response;
             } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }
     public function updateDepartments(Request $request)
    {
        try {
            $data['departmentName'] = $request->input('departmentName');
            $api_url = env('MAIN_API') . 'admin/update-department';
            $method = "put";
            $response = $this->helper->postApiCall($method, $api_url, []);
            return $response;
             } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }

    public function getReports()
    {

        try {
            $location = $this->getLocationsDept();
            $departments = $this->getDepartments();
            $employeesList = $this->helper->employeesList();
            $endDate = (new \DateTime())->format('Y-m-d');
            $startDate = (new \DateTime('-30 days'))->format('Y-m-d');
            $result;
            $api_url = env('MAIN_API') . "admin/report?start_date=$startDate&end_date=$endDate";
            $method = "get-with-token";
            $response = $this->helper->postApiCall($method, $api_url, []);
            if ($response['code'] == 200) {
                $result['code'] = 200;
                $result['msg'] = 'success';
                $result['data'] = $response['data']['data'];
                $reportData = $result;
            } else {
                $result['code'] = 400;
                $result['msg'] = 'No data found';
                $reportData = $result;
            }
            return view("User::Report.report")->with(array('departments' => $departments, 'location_departmnet' => $location, 'reportData' => $reportData, 'employeesList' => $employeesList));
        } catch (\Exception $e) {
            return $this->ExceptionErrorHandler($e, "400", ' UserController => getDepartments => Method-get');
        }
    }

    public function getReportData(Request $request)
    {
        try {
            $locationId = $request->input('location_id');
            $departmentId = $request->input('department_id');
            $employeeId = $request->input('employee_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            // Build query parameters
            $queryParams = [];
            if ($fromDate) {
                $queryParams['start_date'] = $fromDate;
            }
            if ($toDate) {
                $queryParams['end_date'] = $toDate;
            }
            if ($locationId) {
                $queryParams['location_id'] = $locationId;
            }
            if ($departmentId) {
                $queryParams['department_id'] = $departmentId;
            }
            if ($employeeId) {
                $queryParams['employee_id'] = $employeeId;
            }

            $queryString = http_build_query($queryParams);
            $api_url = env('MAIN_API') . "admin/report?" . $queryString;
            $method = "get-with-token";
            $response = $this->helper->postApiCall($method, $api_url, []);

            if ($response['code'] == 200) {
                return response()->json([
                    'code' => 200,
                    'data' => $response['data']['data'] ?? [],
                    'message' => 'Success'
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'data' => [],
                    'message' => $response['message'] ?? 'No data found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'data' => [],
                'message' => 'Internal server error: ' . $e->getMessage()
            ]);
        }
    }
    public function productivityRanking(Request $request)
    {
      
        $responseData = [];
        if ($request->isMethod('get')) {
            $api_url = env('MAIN_API')  . 'admin/productivity-rules?skip=0&limit=10' ;
            $method = "get-with-token";
            $response = $this->helper->postApiCall($method, $api_url, []);
            if ($response['code'] == 200) {
                $responseData['data'] = $response['data']['data'];
                for ($i = 0; $i < count($responseData['data']); $i++) {
                    if (strpos($responseData['data'][$i]['name'], ".exe") !== false) {
                        $responseData['data'][$i]['name'] = str_replace(".exe", "", $responseData['data'][$i]['name']);
                    }
                    if ($responseData['data'][$i]['type'] != 2) $responseData['data'][$i]['name'] = ucfirst($responseData['data'][$i]['name']);

                }
            }
            $responseData['count'] = $response['data']['count'] ?? 0;
            
            return view('User::Settings.productivity_ranking')->with(['response' => $responseData]);
        } else{
            $api_url = env('MAIN_API')  . 'admin/productivity-rules?skip='.$request->input('skip').'&limit='.$request->input('limit');
            $method = "get-with-token";
            $response = $this->helper->postApiCall($method, $api_url, []);
           if ($response['code'] == 200) {
                 $responseData['code'] = 200;
                $responseData['data'] = $response['data']['data'];
                for ($i = 0; $i < count($responseData['data']); $i++) {
                    if (strpos($responseData['data'][$i]['name'], ".exe") !== false) {
                        $responseData['data'][$i]['name'] = str_replace(".exe", "", $responseData['data'][$i]['name']);
                    }
                    if ($responseData['data'][$i]['type'] != 2) $responseData['data'][$i]['name'] = ucfirst($responseData['data'][$i]['name']);

                }
            }
            $responseData['count'] = $response['data']['count'] ?? 0;
            return $responseData;
        }
    }
     public function productivityUpdate(Request $request)
    {
    
        try {
            $data['_id'] = $request->input('id');
            $data['category'] = $request->input('category');
            $api_url = env('MAIN_API')  . 'admin/productivity-rules' ;
            $method = "put";
            $response = $this->helper->postApiCall($method, $api_url, $data);
            if ($response['code'] == 200) {
                $result['code'] = 200;
                $result['data'] = $response['data'];
            } else {
                $result['code'] = 400;
                $result['message'] = "Productivity update failed";
            }
        } catch (\Exception $e) {
            Log::info('Exception in productivityUpdate ' . $e->getMessage());
            $result['code'] = 400;
            $result['message'] = __('messages.exception');
        }
        return $result;
    }

}
