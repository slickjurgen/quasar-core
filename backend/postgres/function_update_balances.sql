DROP FUNCTION update_balances;

CREATE FUNCTION update_balances() 
    RETURNS trigger AS $BODY$

    BEGIN 

    INSERT INTO account_balances (account_id, balance, reference_date) VALUES (NEW.debit_account_id, -NEW.amount, CURRENT_DATE)
        ON CONFLICT (account_id, reference_date)
        DO
            UPDATE SET balance = account_balances.balance - NEW.amount;
            
    INSERT INTO account_balances (account_id, balance, reference_date) VALUES (NEW.credit_account_id, NEW.amount, CURRENT_DATE)
        ON CONFLICT (account_id, reference_date)
        DO
            UPDATE SET balance = account_balances.balance + NEW.amount;
            
    RETURN NULL;
    
    END;
    $BODY$ LANGUAGE plpgsql;