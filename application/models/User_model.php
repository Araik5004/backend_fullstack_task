<?php

namespace Model;

use App;
use CI_Emerald_Model;
use Exception;
use stdClass;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 27.01.2020
 * Time: 10:10
 */
class User_model extends CI_Emerald_Model {
    const CLASS_TABLE = 'user';


    /** @var string */
    protected $email;
    /** @var string */
    protected $password;
    /** @var string */
    protected $personaname;
    /** @var string */
    protected $profileurl;
    /** @var string */
    protected $avatarfull;
    /** @var int */
    protected $rights;
    /** @var int */
    protected $likes;
    /** @var float */
    protected $wallet_balance;
    /** @var float */
    protected $wallet_total_refilled;
    /** @var float */
    protected $wallet_total_withdrawn;
    /** @var string */
    protected $time_created;
    /** @var string */
    protected $time_updated;


    private static $_current_user;

    /**
     * @return string
     */
    public function get_email(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function set_email(string $email)
    {
        $this->email = $email;
        return $this->save('email', $email);
    }

    /**
     * @return string|null
     */
    public function get_password(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function set_password(string $password)
    {
        $this->password = $password;
        return $this->save('password', $password);
    }

    /**
     * @return string
     */
    public function get_personaname(): string
    {
        return $this->personaname;
    }

    /**
     * @param string $personaname
     *
     * @return bool
     */
    public function set_personaname(string $personaname)
    {
        $this->personaname = $personaname;
        return $this->save('personaname', $personaname);
    }

    /**
     * @return string
     */
    public function get_avatarfull(): string
    {
        return $this->avatarfull;
    }

    /**
     * @param string $avatarfull
     *
     * @return bool
     */
    public function set_avatarfull(string $avatarfull)
    {
        $this->avatarfull = $avatarfull;
        return $this->save('avatarfull', $avatarfull);
    }

    /**
     * @return int
     */
    public function get_rights(): int
    {
        return $this->rights;
    }

    /**
     * @param int $rights
     *
     * @return bool
     */
    public function set_rights(int $rights)
    {
        $this->rights = $rights;
        return $this->save('rights', $rights);
    }

    /**
     * @return int
     */
    public function get_likes(): int
    {
        return $this->likes;
    }
    /**
     * @param int $likes
     *
     * @return bool
     */
    public function set_likes(int $likes)
    {
        $this->likes = $likes;
        return $this->save('likes', $likes);
    }

    /**
     * @return float
     */
    public function get_wallet_balance(): float
    {
        return $this->wallet_balance;
    }

    /**
     * @param float $wallet_balance
     *
     * @return bool
     */
    public function set_wallet_balance(float $wallet_balance)
    {
        $this->wallet_balance = $wallet_balance;
        return $this->save('wallet_balance', $wallet_balance);
    }

    /**
     * @return float
     */
    public function get_wallet_total_refilled(): float
    {
        return $this->wallet_total_refilled;
    }

    /**
     * @param float $wallet_total_refilled
     *
     * @return bool
     */
    public function set_wallet_total_refilled(float $wallet_total_refilled)
    {
        $this->wallet_total_refilled = $wallet_total_refilled;
        return $this->save('wallet_total_refilled', $wallet_total_refilled);
    }

    /**
     * @return float
     */
    public function get_wallet_total_withdrawn(): float
    {
        return $this->wallet_total_withdrawn;
    }

    /**
     * @param float $wallet_total_withdrawn
     *
     * @return bool
     */
    public function set_wallet_total_withdrawn(float $wallet_total_withdrawn)
    {
        $this->wallet_total_withdrawn = $wallet_total_withdrawn;
        return $this->save('wallet_total_withdrawn', $wallet_total_withdrawn);
    }

    /**
     * @return string
     */
    public function get_time_created(): string
    {
        return $this->time_created;
    }

    /**
     * @param string $time_created
     *
     * @return bool
     */
    public function set_time_created(string $time_created)
    {
        $this->time_created = $time_created;
        return $this->save('time_created', $time_created);
    }

    /**
     * @return string
     */
    public function get_time_updated(): string
    {
        return $this->time_updated;
    }

    /**
     * @param string $time_updated
     *
     * @return bool
     */
    public function set_time_updated(string $time_updated)
    {
        $this->time_updated = $time_updated;
        return $this->save('time_updated', $time_updated);
    }


    function __construct($id = NULL)
    {
        parent::__construct();
        $this->set_id($id);
    }

    public function reload(bool $for_update = FALSE)
    {
        parent::reload($for_update);

        return $this;
    }

    public static function create(array $data)
    {
        App::get_ci()->s->from(self::CLASS_TABLE)->insert($data)->execute();
        return new static(App::get_ci()->s->get_insert_id());
    }

    public function delete()
    {
        $this->is_loaded(TRUE);
        App::get_ci()->s->from(self::CLASS_TABLE)->where(['id' => $this->get_id()])->delete()->execute();
        return (App::get_ci()->s->get_affected_rows() > 0);
    }

    /**
     * @return self[]
     * @throws Exception
     */
    public static function get_all():array
    {

        $data = App::get_ci()->s->from(self::CLASS_TABLE)->many();
        $ret = [];
        foreach ($data as $i)
        {
            $ret[] = (new self())->set($i);
        }
        return $ret;
    }


    /**
     * Getting id from session
     * @return integer|null
     */
    public static function get_session_id(): ?int
    {
        return App::get_ci()->session->userdata('id');
    }

    /**
     * @return bool
     */
    public static function is_logged():bool
    {
        $steam_id = intval(self::get_session_id());
        return $steam_id > 0;
    }



    /**
     * Returns current user or empty model
     * @return User_model
     */
    public static function get_user()
    {
        if (! is_null(self::$_current_user)) {
            return self::$_current_user;
        }
        if ( ! is_null(self::get_session_id()))
        {
            self::$_current_user = new self(self::get_session_id());
            return self::$_current_user;
        } else
        {
            return new self();
        }
    }



    /**
     * @param User_model|User_model[] $data
     * @param string $preparation
     * @return stdClass|stdClass[]
     * @throws Exception
     */
    public static function preparation($data, $preparation = 'default')
    {
        switch ($preparation)
        {
            case 'main_page':
                return self::_preparation_main_page($data);
            case 'default':
                return self::_preparation_default($data);
            default:
                throw new Exception('undefined preparation type');
        }
    }

    /**
     * @param User_model $data
     * @return stdClass
     */
    private static function _preparation_main_page($data)
    {
        $o = new stdClass();

        $o->id = $data->get_id();

        $o->personaname = $data->get_personaname();
        $o->avatarfull = $data->get_avatarfull();

        $o->time_created = $data->get_time_created();
        $o->time_updated = $data->get_time_updated();


        return $o;
    }


    /**
     * @param User_model $data
     * @return stdClass
     */
    private static function _preparation_default($data)
    {
        $o = new stdClass();

        if (!$data->is_loaded())
        {
            $o->id = NULL;
        } else {
            $o->id = $data->get_id();

            $o->personaname = $data->get_personaname();
            $o->avatarfull = $data->get_avatarfull();

            $o->time_created = $data->get_time_created();
            $o->time_updated = $data->get_time_updated();
        }

        return $o;
    }

    /**
     * @param string $email
     * @return self
     * @throws Exception
     */
    public static function get_user_by_email(string $email)
    {
        $data = App::get_ci()->s
            ->from(self::CLASS_TABLE)
            ->where('email' , $email)
            ->one()
        ;
        if(empty($data))
        {
            return false;
        }
        $user = new self($data['id']);
        return $user;

    }
    /**
     * @param User_model $user
     * @param float $sum
     * @return bool
     * @throws Exception
     */
    public static function add_balance(User_model $user , float $sum)
    {

        try {
            //Update user balance
            App::get_ci()->s
                ->from('user')
                ->where(['id' => $user->get_id()])
                ->update(['wallet_balance' => ($user->get_wallet_balance() + $sum)
                    , 'wallet_total_refilled' => ($user->get_wallet_total_refilled() + $sum)
                ])
                ->execute();

            $affected_rows_user = App::get_ci()->s->get_affected_rows();
        }catch (Exception $e)
        {
            return false;
        }

        if($affected_rows_user < 1)
        {
            return false;
        }

        $user->reload();
        return true;

    }

    /**
     * @param User_model $user
     * @param Boosterpack_model $booster_pack
     * @return bool
     * @throws Exception
     */
    public static function user_buy_booster_pack(User_model $user , Boosterpack_model $booster_pack)
    {

        $price_booster_pack = $booster_pack->get_price();
        $bank_booster_pack = $booster_pack->get_bank();

        $possible_like_count = $price_booster_pack + $bank_booster_pack;
        $user_rand_likes = rand( 1 , $possible_like_count);
        $new_bank = ($bank_booster_pack + $price_booster_pack) - $user_rand_likes;


        try {
            App::get_ci()->s->set_transaction_repeatable_read()->execute();
            App::get_ci()->s->start_trans()->execute();

            //Update booster pack bank
            App::get_ci()->s
                ->from('boosterpack')
                ->where(['id' => $booster_pack->get_id()])
                ->update(['bank' => $new_bank])
                ->execute()
            ;
            $affected_rows_booster_pack = App::get_ci()->s->get_affected_rows();

            //Update user balance , and like count
            App::get_ci()->s
                ->from('user')
                ->where(['id' => $user->get_id()])
                ->update([
                    'wallet_total_withdrawn' => ($user->get_wallet_total_withdrawn() + $price_booster_pack)
                    ,'wallet_balance' => ($user->get_wallet_balance() - $price_booster_pack)
                    ,'likes' => ($user->get_likes() + $user_rand_likes)
                ])
                ->execute()
            ;
            $affected_rows_user = App::get_ci()->s->get_affected_rows();

        } catch (Exception $e) {
            //something went wrong ,rollback transaction
            App::get_ci()->s->rollback()->execute();
            return false;
        }

        if($affected_rows_booster_pack < 1 || $affected_rows_user < 1)
        {
            //rollback transaction
            App::get_ci()->s->rollback()->execute();
            return false;
        }

        //success commit transaction
        App::get_ci()->s->commit()->execute();
        $user->reload();
        return $user_rand_likes;

    }
}
