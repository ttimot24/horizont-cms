<?php

namespace App\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Model\Page;

class WebsiteController extends Controller
{

    private $request;

    private $engine;

    public function __construct(Request $request){
        $this->request = $request;
        if(app()->isInstalled()){
            $this->engine = app()->make(\App\Interfaces\ThemeEngineInterface::class);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page = null)
    {
        return $this->show($page);
    }

    public function show($page){
        if (\Session::has("lang")) {
            \App::setLocale(\Session::get("lang"));
        }
        if ($this->request->has("lang")) {
            \App::setLocale($this->request->input("lang"));
        }

        $engines = config('horizontcms.theme_engines');

        $this->engine->boot();

        $this->engine->runScript('before');


        if ($this->request->settings['website_down'] == 1 && (\Auth::user() == null || !\Auth::user()->isAdmin())) {
            return $this->engine->renderWebsiteDown();
        }


        $requested_page = empty($page)? Page::find($this->request->settings['home_page']) : Page::findBySlug($page);


        \App\Model\Visits::newVisitor($this->request);


        if (!empty($requested_page)) {
            if (!empty($requested_page->url) && $this->engine->templateExists($requested_page->url)) {
                $template = "page_templates." . $requested_page->url;
            } else {
                $template = 'page';
            }
        } else {
            return $this->engine->render404();
        }


        $this->engine->pageTemplate($template);


        $this->engine->runScript('before_render');


        return $this->engine->render([
            '_REQUESTED_PAGE' => $requested_page,
        ]);
    }

    public function registration()
    {

        if ($this->request->isMethod('POST')) {

            $user = new \App\Model\User($this->request->all());
            $user->active = 0;

            if ($user->save()) {
                return redirect()->back()->withMessage(['success' => trans('message.successfully_created_user')])->withUser($user);
            }
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }


    public function authenticate()
    {

        if ($this->request->isMethod('POST')) {

            $mode = $this->request->has('email') ? 'email' : 'username';

            if (\Auth::attempt([$mode => $this->request->input($mode), 'password' => $this->request->input('password')]) && \Auth::user()->isActive()) {

                $redirect = $this->request->has('redirect_success')? redirect($this->request->input('redirect_success')) : redirect()->back();

                return $redirect->withMessage(['success' => trans('message.successfully_logged_in')]);
            } else {
                \Auth::logout();
            }
        }

        return redirect()->back()->withMessage(['danger' => trans('auth.failed')]);
    }

    public function language()
    {

        if ($this->request->has("lang")) {
            \Session::put('lang', $this->request->input("lang"));
        }

        return redirect()->back();
    }

    public function search()
    {

        if ($this->request->isMethod('POST')) {

            if ($this->request->input('search') == "" || $this->request->input('search') == null) {
                return redirect()->back();
            }


            $search_engine = new \App\Services\SearchEngine();

            $search_engine->registerModel(\App\Model\Blogpost::class);
            $search_engine->registerModel(\App\Model\Page::class);
            $search_engine->registerModel(\App\Model\User::class);

            foreach (config('theme:search.models', []) as $model) {
                $search_engine->registerModel($model);
            }

            $search_engine->executeSearch($this->request->input('search'));

            return redirect(\App\Model\Page::withTemplate('search.php')->first()->getSlug())->withSearchResult(
                $search_engine
            );
        }

        return redirect()->back();
    }

    public function logout()
    {

        \Auth::logout();

        $redirect = $this->request->has('redirect') ? redirect($this->request->input('redirect')) : redirect()->back();

        return $redirect;
    }
}
