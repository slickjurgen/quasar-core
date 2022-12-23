DROP FUNCTION insert_balances_hist;

CREATE FUNCTION insert_balances_hist() 
    RETURNS trigger AS $BODY$

    BEGIN 

    INSERT INTO account_balances_hist (account_id, balance) VALUES (NEW.account_id, NEW.balance);

    RETURN NULL;
    
    END;
    $BODY$ LANGUAGE plpgsql;