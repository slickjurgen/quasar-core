DROP FUNCTION update_balances;

CREATE FUNCTION update_balances() 
    RETURNS trigger AS $BODY$

    BEGIN 

    INSERT INTO account_balances (account_id, balance) VALUES (NEW.debit_account_id, -NEW.amount)
        ON CONFLICT (account_id)
        DO
            UPDATE SET balance = account_balances.balance - NEW.amount;
            
    INSERT INTO account_balances (account_id, balance) VALUES (NEW.credit_account_id, NEW.amount)
        ON CONFLICT (account_id)
        DO
            UPDATE SET balance = account_balances.balance + NEW.amount;

    RETURN NULL;
    
    END;
    $BODY$ LANGUAGE plpgsql;