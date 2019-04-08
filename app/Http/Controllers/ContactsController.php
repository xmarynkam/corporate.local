<?php

namespace Corp\Http\Controllers;

use Illuminate\Http\Request;

use Mail;

class ContactsController extends SiteController
{
    public function __construct() {
        parent::__construct(new \Corp\Repositories\MenusRepository(new \Corp\Menu));
        
        // According to template styles
        $this->bar = 'left';

        // According to the theme of the template
        $this->template = env('THEME').'.contacts';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->title = 'Контакти';
        $this->keywords = 'Контакти';
        $this->meta_desc = 'Контакти';
        
        if($request->isMethod('post')) {
            //
            $massages = [
                            'required' => "Поле :attribute обов'язково має бути заповненим",
                            'email' => "Поле :attribute повинно відповідти формату email-адреси"
                        ];

            $this->validate($request, [
                                        'name' => 'required|max:255',
                                        'email' => 'required|email',
                                        'text' => 'required'
                                        ], $massages);

            $data = $request->all();

            // Send massage on the mail
            Mail::send(env('THEME').'.email', ['data' => $data], function($massage) use ($data) {

                $mail_admin = env('MAIL_USERNAME');


                $massage->from($data['email'], $data['name']);
                $massage->to($mail_admin)->subject('Question');

            });

            

            if(!count(Mail::failures()) > 0) {

                return redirect()->route('contacts')->with('status', 'Email is send');

            }
        }

        $content = view(env('THEME').'.contact_content')->render();
        $this->vars = array_add($this->vars, 'content', $content);

        $this->contentLeftBar = view(env('THEME').'.contact_bar')->render();
        
        return $this->renderOutput();
    }
}
