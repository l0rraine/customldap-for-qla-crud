<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/21
 * Time: 14:35
 */

namespace Qla\CustomLdap;


use Adldap\Laravel\Auth\DatabaseUserProvider;
use Adldap\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;


class CustomAdldapUserProvider extends DatabaseUserProvider
{

    public function validateCredentials(Authenticatable $model, array $credentials)
    {
        // We'll check if we have an LDAP user, and then make sure
        // they pass authentication before going further.

        if (
            $this->user instanceof User &&
            $this->getResolver()->authenticate($this->user, $credentials)
        ) {
            $this->handleAuthenticatedWithCredentials($this->user, $model);

            // Here we will perform authorization on the LDAP user. If all
            // validation rules pass, we will allow the authentication
            // attempt. Otherwise, it is automatically rejected.
            if ($this->newValidator($this->getRules($this->user, $model))->passes()) {
                // We'll check if we've been given a password and that
                // syncing password is enabled. Otherwise we'll
                // use a random 16 character string.
                if ($this->isSyncingPasswords()) {
                    $password = $credentials['password'];
                } else {
                    $password = str_random();
                }

                // If the model has a set mutator for the password then we'll
                // assume that we're using a custom encryption method for
                // passwords. Otherwise we'll bcrypt it normally.
                $model->password = $model->hasSetMutator('password') ?
                    $password : bcrypt($password);


                // 去掉 -胜利油田
                $model->name = explode('-', $model->name)[0];

                // All of our validation rules have passed and we can
                // finally save the model in case of changes.
                $model->save();

                // If binding to the eloquent model is configured, we
                // need to make sure it's available during the
                // same authentication request.
                if ($this->isBindingUserToModel($model)) {
                    $model->setLdapUser($this->user);
                }

                return true;
            }
        }

        if ($this->isFallingBack() && $model->exists) {
            // If the user exists in our local database already and fallback is
            // enabled, we'll perform standard eloquent authentication.
            return $this->fallback->validateCredentials($model, $credentials);
        }

        return false;
    }


}