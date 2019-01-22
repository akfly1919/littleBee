<?php
namespace api\models;

use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $openid;
    public $phone;
    public $password;
    public $rememberMe = true;

    private $_team;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login($sessionKey)
    {
        if ($this->validate()) 
        {
            $this->getUser();
            $this->onGenerateApiToken ($sessionKey);
            
            if (Yii::$app->user->login($this->_team, $this->rememberMe ? 3600 * 24 : 0))
            {
                if ($team = new Team() instanceof IdentityInterface)
                {
                    $_team = Team::findOne(['openid' => $this->openid]);
                    
                    return $_team->api_token;
                }
                
                throw new \ErrorException("服务器错误");
            }
                
            throw new \ErrorException("登录失败, 请稍后重试");
        }
        
        return false;
    }
    
    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function appLogin($action = 'register')
    {
        
        if ($this->validate())
        {
            $this->appGetUser();
            Tools::log("loginForm after appGetUser", $action, $this->_team->id);
            $this->onAppGenerateApiToken ();
            
            /* 改成不需要密码
            // 登录时
            if($action !== 'register')
            {
                // 公众号用户需要设置密码
                if(empty($this->_team->password))
                    throw new \ErrorException("密码为空，需要设置密码", 10001);
                
                $validatePassword = Yii::$app->getSecurity()->validatePassword($this->password, $this->_team->password);
                // 验证密码是否正确
                if ($validatePassword)
                    return $this->processLogin();
                
                throw new \ErrorException('密码错误');
            } */
            
            return $this->processLogin();
        }
        
        throw new \ErrorException('验证失败');
    }
    
    private function processLogin()
    {
        if (Yii::$app->user->login($this->_team, $this->rememberMe ? 3600 * 24 : 0))
        {
            if ($team = new Team() instanceof IdentityInterface)
                return $this->_team->api_token;
            
            throw new \ErrorException('异常错误');
        }
        
        throw new \ErrorException('登录失败，请重试');
    }
    
    /**
     * 获取teamID/添加team数据
     * @param string  $openID openid
     * @return boolean
     */
    public function getTeamID($openID)
    {
        $team = Team::findOne(['openid' => $openID]);
        
        // 不存在时添加一条数据
        if(!$team)
        {
            $team = new Team();
            $team->openid = $openID;
            $team->save();
        }
        
        return $team->id;
    }
    
    /**
     * 获取teamID/添加team数据
     * @param string  $openID openid
     * @return boolean
     */
    public function appGetTeamID($password = "")
    {
        $team = Team::findOne(['phone' => $this->phone]);
        
        // 不存在时添加一条数据
        if(!$team)
        {
            $team = new Team();
            
            $team->phone    = $this->phone;
            $team->password = $password;
            $team->save();
        }
        
        return $team->id;
    }

    /**
     * Finds user by [[openid]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_team === null) {
            $this->_team = Team::findOne(['openid' => $this->openid]);
        }

        return $this->_team;
    }
    
    /**
     * Finds user by [[phone]]
     *
     * @return User|null
     */
    protected function appGetUser()
    {
        if ($this->_team === null) {
            $this->_team = Team::findOne(['phone' => $this->phone]);
        }
        
        return $this->_team;
    }
    
    /**
     * 登录校验成功后，为用户生成新的token
     * 如果token失效，则重新生成token
     */
    public function onGenerateApiToken ($sessionKey)
    {
        if (!Team::apiTokenIsValid($this->_team->api_token)) {
            $this->_team->generateApiToken($sessionKey, $this->_team->id);
            $this->_team->save(false);
        }
    }
    
    /**
     * 登录校验成功后，为用户生成新的token
     * 如果token失效，则重新生成token
     */
    public function onAppGenerateApiToken ()
    {
        if (!Team::apiTokenIsValid($this->_team->api_token)) {
            $this->_team->appGenerateApiToken($this->_team->id);
            $this->_team->save(false);
        }
    }
}
