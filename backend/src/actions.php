<?php

namespace cbos;
use \Date;

class actions
{
    public $hasura;


    public function __construct($hasura)
    {
        $this->hasura = $hasura;
    }

    public function get_hello()
    {
        // print_r($this->hasura);
        return '** hello actions **';
    }

    public function begin_maturity_period($data)
    {
        // everything is fine
        $rc = true;

        dbg('++++ Input', $data['input']);

        $account_id = $data['input']['input']['accountId'];
        $term = $data['input']['input']['term'];

        $resp = post_graphql($this->hasura, 'get_interest_rate_balance_deposit_accounts_accounts_by_pk', ['id' => $account_id]);
        /* if ($resp['errors']) {
            return $resp;
        } */
        dbg('++++ Get interest rate from Hasura', $resp);

        if(count($resp['data']['accounts_by_pk']['account_balances']) != 1 || count($resp['data']['accounts_by_pk']['deposit_accounts']) != 1) {
            
            $rc = false;
            dbg('++++ ERROR did\'t get expected values from Hasura...');

        } else {
            $interest_rate = $resp['data']['accounts_by_pk']['deposit_accounts'][0]['interest_rate'];
            $balance = $resp['data']['accounts_by_pk']['account_balances'][0]['balance'];

            dbg('++++ Interest rate', $interest_rate);
            dbg('++++ Balance', $balance);

            // first let's do the calculations
            // here we asume interest is payed on maturity
            $maturity_date = date('Y-m-d', strtotime("+$term months"));
            dbg('++++ Maturity', $maturity_date);

            $interest = ($balance / 100) * $interest_rate / 12 * $term;

            dbg('++++ Assume interest is paid on maturity (this works also with periods of less than a year)');
            dbg('++++ Interest', $interest);

            // Save this
            $resp = post_graphql($this->hasura, 'insert_deposit_interest_one', ['account_id' => $account_id, 'interest' => $interest, 'value_date' => $maturity_date]);

            // ... and set maturity date finally
            $resp = post_graphql($this->hasura, 'update_deposit_accounts_set_maturity', ['account_id' => $account_id, 'maturity' => $maturity_date]);

            // do this only if term is more than a year or multiple of years
            if($term > 11 && $term % 12 == 0) {
                dbg('++++ Assume interest is paid yearly');

                $calc_balance = $balance;
    
                $years = $term / 12;
    
                for($i = 1; $i <= $years; $i++) {
                    $interest = ($calc_balance / 100) * $interest_rate;
    
                    // maybe we should have a flag, if interest will be booked on account 
                    $calc_balance += $interest;
                }
            }

            $interest = $calc_balance - $balance;
            dbg('++++ Interest', $interest);

            dbg('++++ Assume interest is paid monthly');

            $calc_balance = $balance;

            for($i = 1; $i <= $term; $i++) {
                $interest = ($calc_balance / 100) * $interest_rate / 12;

                // maybe we should have a flag, if interest will be booked on account 
                $calc_balance += $interest;
            }

            $interest = $calc_balance - $balance;
            dbg('++++ Interest', $interest);
        }

        return ['success' => $rc];
    }

    public function calc_interest($data) {

    }

}