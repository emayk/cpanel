<?php namespace Stevemo\Cpanel\Controllers;

use View;
use Config;
use Input;
use Sentry;
use Redirect;
use Lang;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\WrongPasswordException;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;


class CpanelController extends BaseController {


    /**
     * Show the dashboard
     *  
     * @author Steve Montambeault
     * @link   http://stevemo.ca
     *  
     * @return Response 
     */
    public function index()
    {
        return View::make(Config::get('cpanel::views.dashboard'));
    }

    /**
     * Show the login form
     *  
     * @author Steve Montambeault
     * @link   http://stevemo.ca
     *  
     * @return Response 
     */
    public function getLogin()
    {
        $login_attribute = Config::get('cartalyst/sentry::users.login_attribute');
        return View::make(Config::get('cpanel::views.login'), compact('login_attribute'));
    }

    /**
     * Authenticate the user
     *
     * @author Steve Montambeault
     * @link   http://stevemo.ca
     *
     *
     * @return Response
     */
    public function postLogin()
    {
        try
        {
            $remember = Input::get('remember_me', false);
            $userdata = array(
                Config::get('cartalyst/sentry::users.login_attribute') => Input::get('login_attribute'),
                'password' => Input::get('password')
            );

            $user = Sentry::authenticate($userdata, $remember);

            return Redirect::intended('admin')->with('success', Lang::get('cpanel::users.login_success'));
        }
        catch (LoginRequiredException $e)
        {
            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
        }
        catch (PasswordRequiredException $e)
        {
            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
        }
        catch (WrongPasswordException $e)
        {
            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
        }
        catch (UserNotActivatedException $e)
        {
            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
        }
        catch (UserNotFoundException $e)
        {
            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
        }
        catch (UserSuspendedException $e)
        {
            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
        }
        catch (UserBannedException $e)
        {
            return Redirect::back()->withInput()->with('login_error',$e->getMessage());
        }
    }
    
}