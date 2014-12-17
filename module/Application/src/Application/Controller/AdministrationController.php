<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 管理的Controller, 用于完成系统的监控和管理操作.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class AdministrationController extends AbstractActionController
{
    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    private function getResultSetArray($resultSet)
    {
        $returnArray = array();
        
        if ( $resultSet == null ) {
            return $returnArray;
        }
        foreach ( $resultSet as $rowSet ) {
            $rowArray = (array)$rowSet;
            array_push($returnArray, $rowArray);
        }
        return $returnArray;
    }

    /**
     * 显示系统管理页面.
     * @return 一个包含页面所需信息的数组
     */
    public function indexAction()
    {
    	if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/dashboard');
        }

        return array(
            'profile'           => $this->getUserProfile(),
            'uncheckUsers'      => $this->getUncheckUsers(),
        );
    }

    /**
     * 检查用户是否已经登录.
     * @return 用户是否已经登录
     */
    private function isAllowedToAccess()
    {
        $session    = new Container('itp_session');
        return $session->offsetExists('isLogined');
    }

    /**
     * HTTP重定向请求.
     * @param  String $redirectPath - 重定向的相对路径
     * @return HTTP重定向请求的对象
     */
    private function sendRedirect($redirectPath)
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * 获取网站的基础路径(如localhost/itp).
     * @return 网站的基础路径
     */
    private function basePath()
    {
        $renderer   = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url        = $renderer->basePath();
        return $url;
    }

    /**
     * 获取用户的基本信息.
     * @return 一个包含用户基本信息的数组
     */
    private function getUserProfile()
    {
        $session    = new Container('itp_session');
        return array(
            'uid'           => $session->offsetGet('uid'),
            'username'      => $session->offsetGet('username'),
            'userGroupSlug' => $session->offsetGet('userGroupSlug'),
            'email'         => $session->offsetGet('email'),
        );
    }

    /**
     * 获取所请求页面的内容.
     * @return 包含页面内容的HTML字符串
     */
    public function getPageContentAction()
    {
        $pageName = $this->params()->fromQuery('pageName');
        $pageData = $this->getPageData($pageName);
        $view     = new ViewModel($pageData);
        $view->setTerminal(true);

        $template = "application/administration/dashboard/$pageName.phtml";
        $resolver = $this->getEvent()
                         ->getApplication()
                         ->getServiceManager()
                         ->get('Zend\View\Resolver\TemplatePathStack');
        
        if ( !$resolver->resolve($template) ) {
            return $this->notFoundAction();
        }
        $view->setTemplate($template);
        return $view;
    }

    /**
     * 加载页面所需数据.
     * @param  String $pageName - 获取页面的名称
     * @return 一个包含页面所需数据的数组
     */
    private function getPageData($pageName)
    {
        $pageName   = ucfirst($pageName);
        $function   = 'get'.$pageName.'PageData';

        return $this->$function();
    }

    private function getDashboardPageData()
    {

    }

    /**
     * 获取用户管理页面所需数据.
     * @return 一个包含用户管理页面所需数据的数组
     */
    private function getUsersPageData()
    {
        return array(
            'totalUsers'        => $this->getTotalUsers(),
            'uncheckUsers'      => $this->getUncheckUsers(),
        );
    }

    /**
     * 获取所有用户的数量.
     * @return 所有用户的数量
     */
    private function getTotalUsers()
    {
        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');

        return $userTable->getCountUsingFilters();
    }

    /**
     * 获取未审核的用户的数量.
     * @return 未审核的用户的数量
     */
    private function getUncheckUsers()
    {
        $userGroupId    = 0;
        $isInspected    = 0;

        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');

        return $userTable->getCountUsingFilters($userGroupId, $isInspected);
    }

    /**
     * 通过用户组的唯一标识符获取用户组的唯一英文缩写.
     * @param  String $userGroupSlug - 用户组的唯一英文缩写
     * @return 用户组的唯一标识符
     */
    private function getUserGroupId($userGroupSlug)
    {
        $serviceManager = $this->getServiceLocator();
        $userGroupTable = $serviceManager->get('Application\Model\UserGroupTable');
        $userGroup      = $userGroupTable->getUserGroupUsingSlug($userGroupSlug);

        if ( $userGroup != null ) {
            return $userGroup->userGroupId;
        } 
        return 0;
    }

    /**
     * 根据筛选条件获取用户列表.
     * @return 一个包含用户信息的JSON数组
     */
    public function getUsersAction()
    {
        $NUMBER_OF_USERS_PER_PAGE   = 25;
        $userGroupSlug              = $this->params()->fromQuery('userGroup');
        $isInspected                = $this->params()->fromQuery('isInspected', -1);
        $isApproved                 = $this->params()->fromQuery('isApproved', -1);
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_USERS_PER_PAGE;
        $userGroupId                = $this->getUserGroupId($userGroupSlug);

        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');
        $users          = $userTable->getUsersUsingFilters($userGroupId, $isInspected, $isApproved, $offset, $NUMBER_OF_USERS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $users != null && $users->count() != 0,
            'users'         => $this->getResultSetArray($users),
        );

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 根据筛选条件获取用户列表分页总数.
     * @return 用户列表分页总数
     */
    public function getUserTotalPagesAction()
    {
        $NUMBER_OF_USERS_PER_PAGE   = 25;
        $userGroupSlug              = $this->params()->fromQuery('userGroup');
        $isApproved                 = $this->params()->fromQuery('isApproved', -1);
        $userGroupId                = $this->getUserGroupId($userGroupSlug);

        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');
        $totalPages     = ceil($userTable->getCountUsingFilters($userGroupId, $isApproved) / $NUMBER_OF_USERS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 使用关键字搜索用户.
     * @return 一个包含用户信息的JSON数组
     */
    public function getUsersUsingKeywordAction()
    {
        $NUMBER_OF_USERS_PER_PAGE   = 10;
        $keyword                    = $this->params()->fromQuery('keyword');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_USERS_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');
        $users          = $userTable->getUserUsingKeyword($keyword, $offset, $NUMBER_OF_USERS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $users != null && $users->count() != 0,
            'users'         => $this->getResultSetArray($users),
        );

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取课程管理页面所需数据.
     * @return 一个包含课程管理页面所需数据的数组
     */
    private function getCoursesPageData()
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->getAllCourseTypes();
        $totalCourses       = $this->getTotalCourses();
        $publicCourses      = $this->getPublicCourses();

        return array(
            'totalCourses'      => $totalCourses,
            'publicCourses'     => $publicCourses,
            'nonPublicCourses'  => $totalCourses - $publicCourses,
            'courseTypes'       => $courseTypes,
        );
    }

    /**
     * 获取课程的总数量.
     * @return 课程的总数量
     */
    private function getTotalCourses()
    {
        $courseTypeId       = 0;
        $isPublic           = -1;
        $isUserChecked      = -1;

        $serviceManager     = $this->getServiceLocator();
        $courseTable        = $serviceManager->get('Application\Model\CourseTable');
        $totalCourses       = $courseTable->getCountUsingFilters($courseTypeId, $isPublic, $isUserChecked);

        return $totalCourses;
    }

    /**
     * 获取公开课的数量.
     * @return 公开课的数量
     */
    private function getPublicCourses()
    {
        $courseTypeId       = 0;
        $isPublic           = 1;
        $isUserChecked      = -1;

        $serviceManager     = $this->getServiceLocator();
        $courseTable        = $serviceManager->get('Application\Model\CourseTable');
        $publicCourses      = $courseTable->getCountUsingFilters($courseTypeId, $isPublic, $isUserChecked);

        return $publicCourses;
    }

    /**
     * 获取课程列表.
     * @return 一个包含课程信息的JSON数组
     */
    public function getCoursesAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE     = 25;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $isPublic                       = $this->params()->fromQuery('isPublic', -1);
        $isUserChecked                  = $this->params()->fromQuery('isUserChecked', -1);
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $courses        = $courseTable->getCoursesUsingFilters($courseTypeId, $isPublic, $isUserChecked, $offset, $NUMBER_OF_COURSES_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取课程页面数量.
     * @return 一个包含课程页面数量的JSON数组
     */
    public function getCourseTotalPagesAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE     = 25;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $isPublic                       = $this->params()->fromQuery('isPublic', -1);
        $isUserChecked                  = $this->params()->fromQuery('isUserChecked', -1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);

        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $totalPages     = ceil($courseTable->getCountUsingFilters($courseTypeId, $isPublic, $isUserChecked) / $NUMBER_OF_COURSES_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 通过课程类型的唯一英文缩写查找课程类型的唯一标识符.
     * @param  String $catelogySlug - 课程类型的唯一英文缩写
     * @return 课程类型的唯一标识符
     */
    private function getCourseTypeId($catelogySlug)
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseType         = $courseTypeTable->getCatelogyUsingSlug($catelogySlug);

        if ( $courseType != null ) {
            return $courseType->courseTypeId;
        } 
        return 0;
    }

    /**
     * 根据关键字筛选课程.
     * @return 一个包含课程页面数量的JSON数组
     */
    public function getCourseUsingKeywordAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $keyword                    = $this->params()->fromQuery('keyword');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $courses        = $courseTable->getCourseUsingKeyword($keyword, $offset, $NUMBER_OF_COURSES_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
        );

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    private function getLecturesPageData()
    {
        
    }

    private function getRequirementsPageData()
    {
        
    }

    private function getPostsPageData()
    {
        
    }

    private function getSettingsPageData()
    {
        
    }
}