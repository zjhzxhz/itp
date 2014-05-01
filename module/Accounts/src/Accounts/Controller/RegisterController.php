<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Handle requests on the register page.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class RegisterController extends AbstractActionController
{
    /**
     * Default method to call in the controller.
     * @return a ViewModel object which contains HTML content
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Handle asynchronous register requests for the users.
     * @return a HTTP response object which contains JSON data
     *         infers whether the register is successful
     */
    public function processAction()
    {
        $basicInfoArray             = $this->getBasicInfoArray();
        $result                     = $this->verifyBasicInfo($basicInfoArray);
        
        if ( $result['isSuccessful'] ) {
            $result['isSuccessful'] = $this->processBasicAction($basicInfoArray);
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * Create a record in the users table.
     * @param  Array $basicInfoArray - an array which contains basic 
     *         information of the user
     * @return the unique id of the user
     */
    private function processBasicAction($basicInfoArray)
    {
        $this->createSession($basicInfoArray);

        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        $basicInfo          = array(
            'username'      => $basicInfoArray['username'],
            'password'      => md5($basicInfoArray['password']),
            'email'         => $basicInfoArray['email'],
            'user_group_id' => $basicInfoArray['userGroupId'],
        );
        
        return $userTable->createNewUser($basicInfo);
    }

    /**
     * Get basic information within an array.
     * @return an array which contains basic information of the user
     */
    private function getBasicInfoArray()
    {
        $username               = $this->getRequest()->getPost('username');
        $email                  = $this->getRequest()->getPost('email');
        $password               = $this->getRequest()->getPost('password');
        $confirmPassword        = $this->getRequest()->getPost('password-again');
        $userGroupSlug          = $this->getRequest()->getPost('user-group');

        return array(
            'username'          => strip_tags($username),
            'email'             => strip_tags($email),
            'password'          => $password,
            'confirmPassword'   => strip_tags($confirmPassword),
            'userGroupSlug'     => ucfirst(strip_tags($userGroupSlug)),
            'userGroupId'       => strip_tags($this->getUserGroupID($userGroupSlug)),
        );
    }

    /**
     * Verify if the basic information of the user is legal.
     * @param  Array $basicInfo - an array which contains basic information 
     *         of the user
     * @return an array which contains query result
     */
    private function verifyBasicInfo($basicInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isUsernameEmpty'           => empty($basicInfo['username']),
            'isUsernameLegal'           => $this->isUsernameLegal($basicInfo['username']),
            'isUsernameExists'          => $this->isUsernameExists($basicInfo['username']),
            'isEmailEmpty'              => empty($basicInfo['email']),
            'isEmailLegal'              => $this->isEmailLegal($basicInfo['email']),
            'isEmailExists'             => $this->isEmailExists($basicInfo['email']),
            'isPasswordEmpty'           => empty($basicInfo['password']),
            'isPasswordLegal'           => $this->isPasswordLegal($basicInfo['password']),
            'isConfirmPasswordEmpty'    => empty($basicInfo['confirmPassword']),
            'isConfirmPasswordMatched'  => ($basicInfo['password'] == $basicInfo['confirmPassword']),
            'isUserGroupLegal'          => ($basicInfo['userGroupId'] != 0),
        );
        $result['isSuccessful']         = !$result['isUsernameEmpty']        &&  $result['isUsernameLegal']          &&
                                          !$result['isUsernameExists']       && !$result['isEmailEmpty']             && 
                                           $result['isEmailLegal']           && !$result['isEmailExists']            &&
                                          !$result['isPasswordEmpty']        &&  $result['isPasswordLegal']          &&
                                          !$result['isConfirmPasswordEmpty'] &&  $result['isConfirmPasswordMatched'] &&
                                           $result['isUserGroupLegal'];
        return $result;
    }

    /**
     * Verify if the username is legal.
     * Rule: the username should start with a character, and the length of
     *       it should no less than 6 characters and no more than 16 characters.
     *       It should be conbined with characters, numbers and underlines.
     * 
     * @param  String  $username - the username of the user
     * @return true if the username is legal
     */
    private function isUsernameLegal($username)
    {
        return (bool)preg_match('/^[a-z][0-9a-z_]{5,15}$/i', $username);
    }

    /**
     * Verify if the username has existed.
     * @param  String  $username - the username of the user
     * @return true if the username has existed
     */
    private function isUsernameExists($username)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        return $userTable->isUsernameExists($username);
    }

    /**
     * Verify if the email address is legal.
     * @param  String  $email - the email address of the user
     * @return true if the email is legal
     */
    private function isEmailLegal($email)
    {
        return (bool)preg_match('/^[A-Z0-9._%-]{4,18}@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);
    }

    /**
     * Verify if the email address has existed.
     * @param  String  $email - the email address of the user
     * @return true if the email has existed
     */
    private function isEmailExists($email)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        return $userTable->isEmailExists($email);
    }

    /**
     * Verify if the password is legal.
     * Rule: the length of the password should no less than 6 characters,
     *       and no more than 16 characters.
     * 
     * @param  String  $password - the password of the user
     * @return true if the password is legal
     */
    private function isPasswordLegal($password)
    {
        $MIN_LENGTH_OF_PASSWORD     = 6;
        $MAX_LENGTH_OF_PASSWORD     = 16;
        $length                     = strlen($password);
        return ( $length >= $MIN_LENGTH_OF_PASSWORD && 
                 $length <= $MAX_LENGTH_OF_PASSWORD );
    }

    /**
     * Get the unique id of the user group by its slug.
     * @param  String $userGroupSlug - the unique slug of the user group
     * @return the unique id of the user group
     */
    private function getUserGroupID($userGroupSlug)
    {
        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupID($userGroupSlug);

        if ( $userGroup == null ) {
            return null;
        }
        return $userGroup->user_group_id;
    }

    /**
     * Get the unique slug of the user group by its id.
     * @param  int $userGroupId - the unique id of the user group
     * @return the unique slug of the user group
     */
    private function getUserGroupSlug($userGroupId)
    {
        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupSlug($userGroupId);

        if ( $userGroup == null ) {
            return null;
        }
        return $userGroup->user_group_slug;
    }

    /**
     * Create a session for a registering user.
     * @param  Array $basicInfoArray - an array which contains user's profile
     */
    private function createSession($basicInfoArray)
    {
        $session    = new Container('itp_session');
        
        $session->offsetSet('isLogined', true);
        $session->offsetSet('username', $basicInfoArray['username']);
        $session->offsetSet('email', $basicInfoArray['email']);
        $session->offsetSet('isActivated', false);
        $session->offsetSet('userGroupSlug', 
                            $this->getUserGroupSlug($basicInfoArray['userGroupId']));
    }

    /**
     * Get user profile from the session.
     * @return an array which contains user's profile
     */
    private function getSessionData()
    {
        $session    = new Container('itp_session');

        $sessionData = array(
            'uid'               => $session->offsetGet('uid'),
            'username'          => $session->offsetGet('username'),
            'email'             => $session->offsetGet('email'),
            'userGroupSlug'     => ucfirst($session->offsetGet('userGroupSlug')),
        );
        return $sessionData;
    }

    /**
     * Send HTTP redirect reponse.
     * @param  String $redirectPath - the pasth to redirect
     * @return an HTTP redirect reponse object
     */
    private function sendRedirect($redirectPath = '')
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * Display a HTML content which notice user to check his mailbox.
     * @return a ViewModel object which contains HTML content
     */
    public function verifyEmailAction()
    {
        if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/register');
        }
        if ( $this->isActivated() ) {
            return $this->sendRedirect('accounts/register/completeProfile');
        }

        $this->verifyEmail();        
        return array(
            'email'     => $this->getEmailAddress(),
        );
    }

    /**
     * Verify if the email of the user is valid.
     *
     * The function will add a record to database and send an email
     * to the user.
     */
    private function verifyEmail()
    {
        $email  = $this->getEmailAddress();
        $guid   = $this->getGUID();

        $this->saveToDatabase($email, $guid);
        $this->sendValidationEmail($email, $guid);
    }

    /**
     * Check if the user has logined.
     * @return true if the user has logined
     */
    private function isAllowedToAccess()
    {
        $session    = new Container('itp_session');
        return $session->offsetExists('isLogined');
    }

    /**
     * Get the email of the user.
     * @return an String which infers the email of the user
     */
    private function getEmailAddress()
    {
        $session    = new Container('itp_session');
        return $session->offsetGet('email');
    }

    /**
     * Save activation information in the database.
     * @param  String $email - the email of the user
     * @param  String $guid - the activation code of the account
     */
    private function saveToDatabase($email, $guid)
    {
        $sm                 = $this->getServiceLocator();
        $validationTable    = $sm->get('Accounts\Model\EmailValidationTable');
        $record             = array(
            'email'         => $email,
            'guid'          => $guid,
        );

        return $validationTable->createRecord($record);
    }

    /**
     * Send validation email to the user.
     * @param  String $email - the email of the user
     * @param  String $guid - the activation code of the account
     */
    private function sendValidationEmail($email, $guid)
    {
        $message = new Message();
        $message->addTo($email)
                ->addFrom('noreply@zjhzxhz.com', 'IT培训平台')
                ->setSubject('请完成在IT培训平台的注册')
                ->setBody($this->getMailContent($email, $guid));

        $transport = new SmtpTransport();
        // $transport->send($message);
    }

    /**
     * Get random activation code.
     * @return an random activation code of the account
     */
    private function getGUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf( '%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
                        mt_rand(0, 65535), mt_rand(0, 65535), 
                        mt_rand(0, 65535), mt_rand(16384, 20479), 
                        mt_rand(32768, 49151), mt_rand(0, 65535), 
                        mt_rand(0, 65535), mt_rand(0, 65535) );
    }

    /**
     * Generate the content of the validation email.
     * @param  String $email - the email of the user
     * @param  String $guid - the activation code of the account
     * @return the content of the validation email
     */
    private function getMailContent($email, $guid)
    {
        return "<html><body><a href=\"#email=$email&activation_code=$guid\">单击此处激活账户</a></body></html>";
    }

    /**
     * Handle the activating account requrests of the users.
     * @return an HTTP redirect reponse object
     */
    public function activateAccountAction()
    {
        $email          = $this->getRequest()->getQuery('email');
        $guid           = $this->getRequest()->getQuery('activation_code');

        $isSuccessful   = $this->activateAccount($email, $guid);

        if ( $isSuccessful ) {
            return $this->sendRedirect('accounts/register/completeProfile');
        } else {
            return $this->sendRedirect('accounts/register/verifyEmail');
        }
    }

    /**
     * Handle the activating account requrests of the users.
     * @param  String $email - the email of the user
     * @param  String $guid - the activation code of the account
     * @return true if the operation is successful
     */
    private function activateAccount($email, $guid)
    {
        $sm                 = $this->getServiceLocator();
        $validationTable    = $sm->get('Accounts\Model\EmailValidationTable');
        $isSuccessful       = $validationTable->validateEmail($email, $guid);

        if ( $isSuccessful ) {
            $isActivated    = true;
            $this->updateAccountActivated($email, $isActivated);
        }

        return $isSuccessful;
    }

    /**
     * Update activation status of the account.
     * @param  String $email - the email of the user
     * @param  bool $isActivated - a flag that infers if the account has 
     *         been activated
     */
    private function updateAccountActivated($email, $isActivated)
    {
        $this->updateSessionAccountActivated($isActivated);
        $this->updateDatabaseAccountActivated($email, $isActivated);
    }

    /**
     * Update activation status of the account in session.
     * @param  bool $isActivated - a flag that infers if the account has 
     *         been activated
     */
    private function updateSessionAccountActivated($isActivated)
    {
        $session    = new Container('itp_session');
        $session->offsetSet('isActivated', true);
    }

    /**
     * Update activation status of the account in database.
     * @param  String $email - the email of the user
     * @param  bool $isActivated - a flag that infers if the account has 
     *         been activated
     */
    private function updateDatabaseAccountActivated($email, $isActivated)
    {
        $sm         = $this->getServiceLocator();
        $userTable  = $sm->get('Accounts\Model\UserTable');

        return $userTable->updateAccountActivated($email, $isActivated);
    }
    
    /**
     * Display a HTML content which notice user to complete his profile.
     * @return a ViewModel object which contains HTML content
     */
    public function completeProfileAction()
    {
        if ( !$this->isActivated() ) {
            return $this->sendRedirect('accounts/register/verifyEmail');
        }

        $sessionData        = $this->getSessionData();
        $workPositions      = $this->getWorkPositions();
        $courseTypes        = $this->getCourseTypes();

        return array(
            'username'      => $sessionData['username'],
            'email'         => $sessionData['email'],
            'userGroupSlug' => $sessionData['userGroupSlug'],
            'workPositions' => $workPositions,
            'courseTypes'   => $courseTypes,
        );
    }

    /**
     * Check if the user has verify his email address.
     * @return true if the user has verify his email address
     */
    private function isActivated()
    {
        $session    = new Container('itp_session');
        return $session->offsetGet('isActivated');
    }

    /**
     * Get all available positions for a user.
     * @return all available positions for a user within an array
     */
    private function getWorkPositions()
    {
        $sm                 = $this->getServiceLocator();
        $positionTable      = $sm->get('Accounts\Model\PositionTable');
        $positions          = $positionTable->fetchAll();

        return $this->getWorkPositionsArray($positions);
    }

    /**
     * Get all available positions for a user.
     * @param  ResultSet $resultSet - an object of ResultSet which contains
     *         all information of work positions
     * @return all available positions for a user within an array
     */
    private function getWorkPositionsArray($resultSet)
    {
        $workPositionsArray = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $key => $value ) {
                $workPositionsArray[ $key ] = $value;
            }
        }
        return $workPositionsArray;
    }

    /**
     * [getCourseTypes description]
     * @return [type] [description]
     */
    private function getCourseTypes()
    {
        $sm                 = $this->getServiceLocator();
        $courseTypeTable    = $sm->get('Solutions\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->fetchAll();

        return $this->getCourseTypesArray($courseTypes);
    }

    /**
     * [getCourseTypesArray description]
     * @param  [type] $resultSet [description]
     * @return [type]            [description]
     */
    private function getCourseTypesArray($resultSet)
    {
        $courseTypesArray = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $key => $value ) {
                $courseTypesArray[ $key ] = $value;
            }
        }
        return $courseTypesArray;
    }

    /**
     * Handle asynchronous complete profile requests for the users.
     * @return a HTTP response object which contains JSON data
     *         infers whether the complete profile operation is 
     *         successful
     */
    public function processCompleteProfileAction()
    {
        $sessionData        = $this->getSessionData();
        $uid                = $sessionData['uid'];
        $userGroupSlug      = $sessionData['userGroupSlug'];

        $getInfoArrayFunc   = 'get'.$userGroupSlug.'InfoArray';
        $verifyInfoFunc     = 'verify'.$userGroupSlug.'Info';
        $processActionFunc  = 'process'.$userGroupSlug.'Action';

        $infoArray          = $this->$getInfoArrayFunc();
        $verifyResult       = $this->$verifyInfoFunc($infoArray);
        
        if ( $verifyResult['isSuccessful'] ) {
            $verifyResult['isSuccessful'] = $this->$processActionFunc($uid, $infoArray);
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($verifyResult) );
        return $response;
    }

    /**
     * Get essential information of a person within an array.
     * @return an array which contains essential information of a person
     */
    private function getPersonInfoArray()
    {
        $personName         = $this->getRequest()->getPost('real-name');
        $personRegion       = $this->getRequest()->getPost('region');
        $personProvince     = $this->getRequest()->getPost('province');
        $personCity         = $this->getRequest()->getPost('city');
        $personPositionSlug = $this->getRequest()->getPost('work-position');
        $personWorkTime     = $this->getRequest()->getPost('work-time');
        $personPhone        = $this->getRequest()->getPost('mobile-phone');

        return array(
            'personName'            => strip_tags($personName),
            'personRegion'          => strip_tags($personRegion),
            'personProvince'        => strip_tags($personProvince),
            'personCity'            => strip_tags($personCity),
            'personPositionId'      => $this->getPositionID( $personPositionSlug ),
            'personWorkTime'        => strip_tags($personWorkTime),
            'personPhone'           => strip_tags($personPhone),
        );
    }

    /**
     * Get the unique id of the work position by its slug.
     * @param  String $positionSlug - the unique slug of the work position
     * @return the unique id of the work position
     */
    private function getPositionID($workPositionSlug)
    {
        $sm                 = $this->getServiceLocator();
        $positionTable      = $sm->get('Accounts\Model\PositionTable');
        $position           = $positionTable->getPositionID($workPositionSlug);

        if ( $position == null ) {
            return null;
        }
        return $position->position_id;
    }

    /**
     * Verify if the information of the person is legal.
     * @param  Array $personInfo - an array which contains information of the 
     *         person
     * @return an array which contains query result
     */
    private function verifyPersonInfo($personInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isPersonNameEmpty'         => empty($personInfo['personName']),
            'isPersonNameLegal'         => $this->isNameLegal($personInfo['personName']),
            'isPersonRegionEmpty'       => empty($personInfo['personRegion']),
            'isPersonProvinceEmpty'     => empty($personInfo['personProvince']),
            'isPersonCityEmpty'         => $this->isCityEmpty($personInfo['personCity'], 
                                                              $personInfo['personProvince']),
            'isWorkPositionLegal'       => ( $personInfo['personPositionId'] != null ),
            'isPersonPhoneEmpty'        => empty($personInfo['personPhone']),
            'isPersonPhoneLegal'        => $this->isPhoneNumberLegal($personInfo['personPhone']),
        );
        $result['isSuccessful']   = !$result['isPersonNameEmpty']     &&  $result['isPersonNameLegal'] &&
                                    !$result['isPersonRegionEmpty']   && !$result['isPersonProvinceEmpty'] &&
                                    !$result['isPersonCityEmpty']     &&  $result['isWorkPositionLegal'] &&
                                    !$result['isPersonPhoneEmpty']    &&  $result['isPersonPhoneLegal'];
        return $result;
    }

    /**
     * Verify if the real name of the user is legal.
     * Rule: the max length of the real name should less than 32 characters.
     * 
     * @param  String  $name - the real name of the user
     * @return true if the real name of the user is legal
     */
    private function isNameLegal($name)
    {
        $MAX_LENGTH_OF_NAME    = 32;
        return ( strlen($name) <= $MAX_LENGTH_OF_NAME );
    }

    /**
     * Verify if the phone number of the user is legal.
     * @param  String  $phone - the phone number of the user
     * @return true if the phone number of the user is legal
     */
    private function isPhoneNumberLegal($phone)
    {
        return (bool)preg_match('/^[0-9-]{7,24}$/', $phone);
    }

    /**
     * Check if the city of the user is empty.
     * @param  [type]  $city     [description]
     * @param  [type]  $province [description]
     * @return true if the user hasn't choose his city where he live
     */
    private function isCityEmpty($city, $province)
    {
        $municipalities = array( '北京市', '天津市', '上海市', '重庆市' );
        
        if ( $city != null ) {
            return false;
        } else if ( in_array( $province, $municipalities ) ) {
            return false;
        }

        return true;
    }

    /**
     * Handle asynchronous register requests for a person.
     * @param  int $uid - the unique id of the user
     * @param  Array $personInfoArray - an array which contains essential 
     *         information of a person
     * @return true if the query is successful
     */
    private function processPersonAction($uid, $personInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $personTable        = $sm->get('Accounts\Model\PersonTable');

        $personInfo         = array(
            'uid'                   => $uid,
            'person_name'           => $personInfoArray['personName'],
            'person_region'         => $personInfoArray['personRegion'],
            'person_province'       => $personInfoArray['personProvince'],
            'person_city'           => $personInfoArray['personCity'],
            'person_position_id'    => $personInfoArray['personPositionId'],
            'person_phone'          => $personInfoArray['personPhone'],
        );

        return $personTable->createNewPerson($personInfo);
    }

    /**
     * Get essential information of a teacher within an array.
     * @return an array which contains essential information of a teacher
     */
    private function getTeacherInfoArray()
    {
        $teacherName        = $this->getRequest()->getPost('real-name');
        $teacherRegion      = $this->getRequest()->getPost('region');
        $teacherProvince    = $this->getRequest()->getPost('province');
        $teacherCity        = $this->getRequest()->getPost('city');
        $teacherField       = $this->getRequest()->getPost('teaching-field');
        $teacherCompany     = $this->getRequest()->getPost('company');
        $teacherPhone       = $this->getRequest()->getPost('mobile-phone');
        $teacherWeibo       = $this->getRequest()->getPost('weibo');

        return array(
            'teacherName'       => strip_tags($teacherName),
            'teacherRegion'     => strip_tags($teacherRegion),
            'teacherProvince'   => strip_tags($teacherProvince),
            'teacherCity'       => strip_tags($teacherCity),
            'teacherField'      => strip_tags($teacherField),
            'teacherCompany'    => strip_tags($teacherCompany),
            'teacherPhone'      => strip_tags($teacherPhone),
            'teacherWeibo'      => strip_tags($teacherWeibo),
        );
    }

    /**
     * Verify if the information of the teacher is legal.
     * @param  Array $teacherInfo - an array which contains information of the 
     *         teacher
     * @return an array which contains query result
     */
    private function verifyTeacherInfo($teacherInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isTeacherNameEmpty'        => empty($teacherInfo['teacherName']),
            'isTeacherNameLegal'        => $this->isNameLegal($teacherInfo['teacherName']),
            'isTeacherRegionEmpty'      => empty($teacherInfo['teacherRegion']),
            'isTeacherProvinceEmpty'    => empty($teacherInfo['teacherProvince']),
            'isTeacherCityEmpty'        => $this->isCityEmpty($teacherInfo['teacherCity'], 
                                                              $teacherInfo['teacherProvince']),
            'isTeachingFieldEmpty'      => empty($teacherInfo['teacherField']),
            'isTeachingFieldLegal'      => $this->isTeachingFieldLegal($teacherInfo['teacherField']),
            'isCompanyNameEmpty'        => empty($teacherInfo['teacherCompany']),
            'isCompanyNameLegal'        => $this->isCompanyNameLegal($teacherInfo['teacherCompany']),
            'isTeacherPhoneEmpty'       => empty($teacherInfo['teacherPhone']),
            'isTeacherPhoneLegal'       => $this->isPhoneNumberLegal($teacherInfo['teacherPhone']),
            'isTeacherWeiboLegal'       => $this->isWeiboAccountLegal($teacherInfo['teacherWeibo'])

        );
        $result['isSuccessful']   = !$result['isTeacherNameEmpty']   &&  $result['isTeacherNameLegal'] &&
                                    !$result['isTeacherRegionEmpty'] && !$result['isTeacherProvinceEmpty'] &&
                                    !$result['isTeacherCityEmpty']   &&
                                    !$result['isTeachingFieldEmpty'] &&  $result['isTeachingFieldLegal'] &&
                                    !$result['isCompanyNameEmpty']   &&  $result['isCompanyNameLegal'] &&
                                    !$result['isTeacherPhoneEmpty']  &&  $result['isTeacherPhoneLegal'] &&
                                     $result['isTeacherWeiboLegal'];
        return $result;
    }

    /**
     * [isTeachingFieldLegal description]
     * @param  [type]  $teachingField [description]
     * @return boolean                [description]
     */
    private function isTeachingFieldLegal($teachingField)
    {
        $teachingFields = split( ',', $teachingField );

        if ( count( $teachingFields ) > 3 ) {
            return false;
        }

        foreach ( $teachingFields as $teachingFieldSlug ) {
            $teachingFieldId = $this->getFieldID( $teachingFieldSlug );

            if ( $teachingField == null ) {
                return false;
            }
        }
        return true;
    }

    /**
     * [getFieldID description]
     * @param  [type] $fieldSlug [description]
     * @return [type]            [description]
     */
    private function getFieldID($fieldSlug)
    {
        $sm                 = $this->getServiceLocator();
        $courseTypeTable    = $sm->get('Solutions\Model\CourseTypeTable');
        $courseType         = $courseTypeTable->getCourseTypeID($fieldSlug);

        if ( $courseType == null ) {
            return null;
        }
        return $courseType->course_type_id;
    }

    /**
     * [isCompanyNameLegal description]
     * @param  [type]  $companyName [description]
     * @return boolean              [description]
     */
    private function isCompanyNameLegal($companyName)
    {
        $MAX_LENGTH_OF_COMPANY_NANE = 64;
        return ( strlen($companyName) < $MAX_LENGTH_OF_COMPANY_NANE );
    }

    /**
     * [isWeiboAccountLegal description]
     * @param  [type]  $weiboAccount [description]
     * @return boolean               [description]
     */
    private function isWeiboAccountLegal($weiboAccount)
    {
        $MAX_LENGTH_OF_WEIBO_ACCOUNT = 32;
        return ( strlen($weiboAccount) < $MAX_LENGTH_OF_WEIBO_ACCOUNT );
    }

    /**
     * Handle asynchronous register requests for a teacher.
     * @param  int $uid - the unique id of the user
     * @param  Array $teacherInfoArray - an array which contains essential 
     *         information of a teacher
     * @return true if the query is successful
     */
    private function processTeacherAction($uid, $teacherInfoArray)
    {
        $isBasicInfoSuccessful      = $this->processTeacherInfo($uid, $teacherInfoArray);
        $isTeachingFieldSuccessful  = $this->processTeachingField($uid, 
                                                $teacherInfoArray['teacherField']);

        return ( $isBasicInfoSuccessful && $isTeachingFieldSuccessful );
    }

    private function processTeacherInfo($uid, $teacherInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');

        $teacherInfo        = array(
            'uid'               => $uid,
            'teacher_name'      => $teacherInfoArray['teacherName'],
            'teacher_region'    => $teacherInfoArray['teacherRegion'],
            'teacher_province'  => $teacherInfoArray['teacherProvince'],
            'teacher_city'      => $teacherInfoArray['teacherCity'],
            'teacher_company'   => $teacherInfoArray['teacherCompany'],
            'teacher_phone'     => $teacherInfoArray['teacherPhone'],
            'teacher_weibo'     => $teacherInfoArray['teacherWeibo'],
        );

        return $teacherTable->createNewTeacher($teacherInfo);
    }

    private function processTeachingField($uid, $teachingField)
    {
        return true;
    }

    /**
     * Get essential information of an enterprise within an array.
     * @return an array which contains essential information of an enterprise
     */
    private function getCompanyInfoArray()
    {
        $companyName        = $this->getRequest()->getPost('company-name');
        $companyRegion      = $this->getRequest()->getPost('region');
        $companyProvince    = $this->getRequest()->getPost('province');
        $companyCity        = $this->getRequest()->getPost('city');
        $companyAddress     = $this->getRequest()->getPost('address');
        $companyField       = $this->getRequest()->getPost('company-field');
        $companyScale       = $this->getRequest()->getPost('company-scale');
        $companyPhone       = $this->getRequest()->getPost('phone');

        return array(
            'companyName'       => strip_tags($companyName),
            'companyRegion'     => strip_tags($companyRegion),
            'companyProvince'   => strip_tags($companyProvince),
            'companyCity'       => strip_tags($companyCity),
            'companyAddress'    => strip_tags($companyAddress),
            'companyField'      => strip_tags($companyField),
            'companyScale'      => strip_tags($companyScale),
            'companyPhone'      => strip_tags($companyPhone),
        );
    }

    /**
     * Verify if the information of the company is legal.
     * @param  Array $companyInfo - an array which contains information of the 
     *         company
     * @return an array which contains query result
     */
    private function verifyCompanyInfo($companyInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isCompanyNameEmpty'        => empty($companyInfo['companyName']),
            'isCompanyNameLegal'        => $this->isCompanyNameLegal($companyInfo['companyName']),
            'isCompanyRegionEmpty'      => empty($companyInfo['companyRegion']),
            'isCompanyProvinceEmpty'    => empty($companyInfo['companyProvince']),
            'isCompanyCityEmpty'        => $this->isCityEmpty($companyInfo['companyCity'], 
                                                              $companyInfo['companyProvince']),
            'isCompanyAddressEmpty'     => empty($companyInfo['companyAddress']),
            'isCompanyAddressLegal'     => $this->isAddressLegal($companyInfo['companyAddress']),
            'isCompanyFieldEmpty'       => empty($companyInfo['companyField']),
            'isCompanyFieldLegal'       => $this->isCompanyFieldLegal($companyInfo['companyField']),
            'isCompanyScaleEmpty'       => empty($companyInfo['companyScale']),
            'isCompanyScaleLegal'       => $this->isCompanyScaleLegal($companyInfo['companyScale']),
            'isCompanyPhoneEmpty'       => empty($companyInfo['companyPhone']),
            'isCompanyPhoneLegal'       => $this->isPhoneNumberLegal($companyInfo['companyPhone']),
        );
        $result['isSuccessful']   = !$result['isCompanyNameEmpty']    &&  $result['isCompanyNameLegal'] &&
                                    !$result['isCompanyRegionEmpty']  && !$result['isCompanyProvinceEmpty'] &&
                                    !$result['isCompanyCityEmpty']    && 
                                    !$result['isCompanyAddressEmpty'] &&  $result['isCompanyAddressLegal'] &&
                                    !$result['isCompanyFieldEmpty']   &&  $result['isCompanyFieldLegal'] &&
                                    !$result['isCompanyScaleEmpty']   &&  $result['isCompanyScaleLegal'] &&
                                    !$result['isCompanyPhoneEmpty']   &&  $result['isCompanyPhoneLegal'];
        return $result;
    }

    /**
     * Verify if the address of the company is legal.
     * Rule: the length of the address should no more than 256
     *       characters.
     * 
     * @param  String  $address - the address of the company
     * @return true if the address of the company is legal
     */
    private function isAddressLegal($address)
    {
        $MAX_LENGTH_OF_ADDRESS      = 256;
        return ( strlen($address) < $MAX_LENGTH_OF_ADDRESS );
    }

    /**
     * Verify if the field of the company is legal.
     * Rule: the length of the address should no more than 32
     *       characters.
     * 
     * @param  String  $companyField - the field of the company
     * @return true if the field of the company is legal
     */
    private function isCompanyFieldLegal($companyField)
    {
        $MAX_LENGTH_OF_FIELD        = 128;
        return ( strlen($companyField) < $MAX_LENGTH_OF_FIELD );
    }

    /**
     * Verify if the scale of the company is legal.
     * Rule: the scale should be a number.
     * 
     * @param  String  $companyScale - the scale of the company
     * @return true if the scale of the company is legal
     */
    private function isCompanyScaleLegal($companyScale)
    {
        if ( !is_numeric($companyScale) ) {
            return false;
        }
        return true;
    }

    /**
     * Handle asynchronous register requests for a teacher.
     * @param  int $uid - the unique id of the user
     * @param  Array $teacherInfoArray - an array which contains essential 
     *         information of a teacher
     * @return true if the query is successful
     */
    private function processCompanyAction($uid, $companyInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $companyTable       = $sm->get('Accounts\Model\CompanyTable');

        $companyInfo     = array(
            'uid'               => $uid,
            'company_name'      => $companyInfoArray['companyName'],
            'company_region'    => $companyInfoArray['companyRegion'],
            'company_province'  => $companyInfoArray['companyProvince'],
            'company_city'      => $companyInfoArray['companyCity'],
            'company_address'   => $companyInfoArray['companyAddress'],
            'company_field'     => $companyInfoArray['companyField'],
            'company_scale'     => $companyInfoArray['companyScale'],
            'company_phone'     => $companyInfoArray['companyPhone'],
        );

        return $companyTable->createNewCompany($companyInfo);
    }
}
