<?php
class ThreadController extends AppController
{
    const PAGE_SET = 9;

    public function register()
    {
        $is_error = FALSE;
        $username = Param::get('username');
        $password = Param::get('password');
		$repassword = Param::get('repassword');


        if ($password != $repassword) {
            $is_error = TRUE;
            $error_message = "Password did not match!";
        }

        
        $thread = new Thread;
        $account = new Account;
        $user_exist = $thread->isUserExisting($username, $password);
        $page = Param::get('page_next', 'register');

        switch ($page) {
            case 'register':
                break;
            
            case 'register_end':
                $account->username = $username;
                $account->password = $password;
                $account->repassword = $repassword;

                try {
                    if (!$user_exist) {
                        $user_id = $thread->register($account);
						$_SESSION['username'] = $username; 
                        $_SESSION['user_id'] = $user_id;						
                    } else {
                        $page = 'register';
                    }
                } catch (ValidationException $e) {
                    $page = 'register';
                }
                break;
            
            default:
                throw new NotFoundException("{$page} is not found");
                break;
        }
        
        $this->set(get_defined_vars());
        $this->render($page);
    }


    public function index()
    {
        if(isset($_SESSION['username'])){
			header("Location: /thread/login_end");          			
        }
	
        $invalid = FALSE;
        $username = Param::get('username');
        $password = Param::get('password');
        $login = Thread::getAccount($username, $password);
        $page = Param::get('page_next', 'index');

        switch ($page) {
            case 'index':
                break;

            case 'login_end':
                if (!$login){
                        $page = 'index';
                        $invalid = TRUE;
                }else{
					$_SESSION['username'] = $login['username'];
                    $_SESSION['user_id'] = $login['id']; 
				}
                break;

            default:
                throw new NotFoundException("{$page} is not found");
                break;
        }

        $this->set(get_defined_vars());
        $this->render($page);
		
    }


    public function home()
    {
        check_session();
        $page = Param::get('page');
        $user = array(
            'user_id'=>$_SESSION['user_id'],
            'username'=>$_SESSION['username']
        );
        $tmp = Thread::getAll($user['user_id'], $page);
        $threads = $tmp[0];
        $totalThread = $tmp[1];
        $totalPage = $tmp[2];
        $nums = $tmp[3];
        $start = $tmp[4];
        $previous = $start - Thread::PAGE_MAX;
        $next = $start + Thread::PAGE_MAX;
        $lastPage = $start + self::PAGE_SET;
        $this->set(get_defined_vars());
    }



    public function write()
    {
	    check_session();
        $comment = new Comment;
        $page = Param::get('page');
        $user_id = Param::get('user_id');
        $username = Param::get('username');
        $page = Param::get('page_next', 'write');
        $thread = Thread::get(Param::get('thread_id'));
        $user_comment = Param::get('body');
		
        switch ($page) {

            case 'write':
                break;

            case 'write_end':
                $comment->username = $username;
                $comment->body = $_SESSION['username']." : ".$user_comment;

                try {
                    $thread->write($comment);
                } catch (ValidationException $e) {
                    $page = 'write';
                }
                break;
            
            default:
                throw new NotFoundException("{$page} is not found");
                break;
        }

        $this->set(get_defined_vars());
        $this->render($page);
    }

    public function create()
    {
        check_session();
        $thread = new Thread;
        $comment = new Comment;
        $user = array(
            'user_id'=>Param::get('user_id'),
            'username'=>Param::get('username')
        );
        $title = Param::get('title');
        $thread_exist = Thread::isThreadExisting($title, Param::get('user_id'));
        $page = Param::get('page_next', 'create');
        $user_comment = Param::get('body');
		
        switch ($page) {
            case 'create':
                break;

            case 'create_end':
                $comment->user_id = $user['user_id'];
                $comment->body = $_SESSION['username']." : ".$user_comment;
				$thread->title = $title." by: ".$_SESSION['username'];

                try {
                    if (!$thread_exist) {
                        $thread->create($comment);
                    } else {
                        $page = 'create';
                    }
                } catch (ValidationException $e) {
                    $page = 'create';
                }
                break;

            default:
                throw new NotFoundException("{$page} is not found");
                break;
        }

        $this->set(get_defined_vars());
        $this->render($page);
    }


    public function view()
    {
	    check_session();
        $page = Param::get('thread_id');
        $username = Param::get('username');
        $user_id = Param::get('user_id');
        $thread = Thread::get($page);
        $tmp = $thread->getComments(Param::get('page'));
        $comments = $tmp[0];
        $totalComment = $tmp[1];
        $totalPage = $tmp[2];
        $nums = $tmp[3];
        $start = $tmp[4];
        $page = $tmp[5];
        $previous = $start - Thread::PAGE_MAX;
        $next = $start + Thread::PAGE_MAX;
        $lastPage = $start + self::PAGE_SET;
        $previousPage = $page - 1;
        $this->set(get_defined_vars());
    }

	public function logout()
	{
	   session_destroy();
	   check_session();
	}
	
	public function login_end()
	{ 
        if(isset($_SESSION['user'])){
	        $username = $_SESSION['user'];
		    $login['username'] = $_SESSION['user'];
            $login['id'] = $_SESSION['user_id'];
        }
	}
}
?>