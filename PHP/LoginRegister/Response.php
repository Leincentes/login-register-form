<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/login-register.proto

namespace PHP\LoginRegister;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>PHP.LoginRegister.Response</code>
 */
class Response extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bool success = 1;</code>
     */
    protected $success = false;
    /**
     * Generated from protobuf field <code>string error_message = 2;</code>
     */
    protected $error_message = '';
    /**
     * Generated from protobuf field <code>.PHP.LoginRegister.Users users = 3;</code>
     */
    protected $users = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type bool $success
     *     @type string $error_message
     *     @type \PHP\LoginRegister\Users $users
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\LoginRegister::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bool success = 1;</code>
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Generated from protobuf field <code>bool success = 1;</code>
     * @param bool $var
     * @return $this
     */
    public function setSuccess($var)
    {
        GPBUtil::checkBool($var);
        $this->success = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string error_message = 2;</code>
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Generated from protobuf field <code>string error_message = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setErrorMessage($var)
    {
        GPBUtil::checkString($var, True);
        $this->error_message = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.PHP.LoginRegister.Users users = 3;</code>
     * @return \PHP\LoginRegister\Users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Generated from protobuf field <code>.PHP.LoginRegister.Users users = 3;</code>
     * @param \PHP\LoginRegister\Users $var
     * @return $this
     */
    public function setUsers($var)
    {
        GPBUtil::checkMessage($var, \PHP\LoginRegister\Users::class);
        $this->users = $var;

        return $this;
    }

}
