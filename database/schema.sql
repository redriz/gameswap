CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION set_added_at_on_owner_change()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.user_id IS DISTINCT FROM OLD.user_id THEN
        NEW.added_at = CURRENT_TIMESTAMP;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION set_listing_seller()
RETURNS TRIGGER AS $$
BEGIN
    SELECT user_id INTO NEW.seller_id FROM user_library WHERE id = NEW.user_library_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_accepted_games_limit()
RETURNS TRIGGER AS $$
DECLARE
    group_type VARCHAR(20);
    current_count INT;
    max_allowed INT;
BEGIN
    SELECT trade_group_type INTO group_type FROM listings WHERE id = NEW.listing_id;

    IF group_type = 'all_together' THEN
        max_allowed := 5;
    ELSE
        max_allowed := 10;
    END IF;

    SELECT COUNT(*) INTO current_count FROM listing_accepted_games WHERE listing_id = NEW.listing_id;

    IF current_count >= max_allowed THEN
        RAISE EXCEPTION 'Limite de % jogos atingido para este anuncio', max_allowed;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    gender VARCHAR(20) NOT NULL CHECK (gender IN ('male', 'female', 'prefer_not_to_say')),
    birht_date DATE NOT NULL,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT FALSE,
    active BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS games (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    steam_app_id INT NOT NULL UNIQUE,
    price_steam NUMERIC(10, 2),
    discount_steam SMALLINT DEFAULT 0,
    price_discounted NUMERIC(10, 2),
    cover_url VARCHAR(500),
    developer VARCHAR(150),
    publisher VARCHAR(150),
    release_date DATE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TRIGGER IF EXISTS trigger_games_updated_at ON games;
CREATE TRIGGER trigger_games_updated_at
BEFORE UPDATE ON games
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS genres (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS game_genres (
    id BIGSERIAL PRIMARY KEY,
    game_id BIGINT REFERENCES games(id) ON DELETE CASCADE,
    genre_id BIGINT REFERENCES genres(id) ON DELETE RESTRICT,
    UNIQUE(game_id, genre_id)
);

CREATE TABLE IF NOT EXISTS user_library (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    game_id BIGINT REFERENCES games(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL DEFAULT 'available' CHECK (status IN ('available', 'listed')),
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    origin VARCHAR(20) NOT NULL CHECK (origin IN ('official_store', 'trade', 'purchase')),
    UNIQUE(user_id, game_id)
);

DROP TRIGGER IF EXISTS trigger_library_owner_change ON user_library;
CREATE TRIGGER trigger_library_owner_change
BEFORE UPDATE ON user_library
FOR EACH ROW
EXECUTE FUNCTION set_added_at_on_owner_change();

CREATE TABLE IF NOT EXISTS listings (
    id BIGSERIAL PRIMARY KEY,
    user_library_id BIGINT REFERENCES user_library(id) ON DELETE RESTRICT,
    seller_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    allows_sale BOOLEAN NOT NULL DEFAULT FALSE,
    allows_trade BOOLEAN NOT NULL DEFAULT FALSE,
    price_net NUMERIC(10, 2),
    price_gross NUMERIC(10, 2),
    accepts_any_game BOOLEAN DEFAULT FALSE,
    trade_group_type VARCHAR(20) CHECK (trade_group_type IN ('any_one', 'all_together')),
    status VARCHAR(20) NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'completed', 'cancelled')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (price_net IS NULL OR (price_net >= 1 AND price_net <= 1000)),
    CHECK (price_gross IS NULL OR (price_gross >= 1)),
    CHECK (allows_sale OR allows_trade),
    CHECK (allows_sale = FALSE OR (price_net IS NOT NULL AND price_gross IS NOT NULL))
);

DROP TRIGGER IF EXISTS trigger_listings_updated_at ON listings;
CREATE TRIGGER trigger_listings_updated_at
BEFORE UPDATE ON listings
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trigger_listings_set_seller ON listings;
CREATE TRIGGER trigger_listings_set_seller
BEFORE INSERT ON listings
FOR EACH ROW
EXECUTE FUNCTION set_listing_seller();

CREATE TABLE IF NOT EXISTS listing_accepted_games (
    id BIGSERIAL PRIMARY KEY,
    listing_id BIGINT REFERENCES listings(id) ON DELETE CASCADE,
    game_id BIGINT REFERENCES games(id) ON DELETE RESTRICT,
    UNIQUE(listing_id, game_id)
);

DROP TRIGGER IF EXISTS trigger_check_accepted_games_limit ON listing_accepted_games;
CREATE TRIGGER trigger_check_accepted_games_limit
BEFORE INSERT ON listing_accepted_games
FOR EACH ROW
EXECUTE FUNCTION check_accepted_games_limit();

CREATE TABLE IF NOT EXISTS offers (
    id BIGSERIAL PRIMARY KEY,
    listing_id BIGINT REFERENCES listings(id) ON DELETE CASCADE,
    buyer_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    status VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'accepted', 'rejected', 'cancelled')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TRIGGER IF EXISTS trigger_offers_updated_at ON offers;
CREATE TRIGGER trigger_offers_updated_at
BEFORE UPDATE ON offers
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS offer_games (
    id BIGSERIAL PRIMARY KEY,
    offer_id BIGINT REFERENCES offers(id) ON DELETE CASCADE,
    user_library_id BIGINT REFERENCES user_library(id) ON DELETE RESTRICT,
    UNIQUE(offer_id, user_library_id)
);

CREATE TABLE IF NOT EXISTS platform_bank (
    id SMALLINT PRIMARY KEY DEFAULT 1,
    CHECK (id = 1)
);

INSERT INTO platform_bank (id) VALUES (1) ON CONFLICT DO NOTHING;

CREATE TABLE IF NOT EXISTS ledger_entries (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    bank_id SMALLINT REFERENCES platform_bank(id) ON DELETE RESTRICT,
    amount NUMERIC(10, 2) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    related_listing_id BIGINT REFERENCES listings(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (
        (user_id IS NOT NULL AND bank_id IS NULL) OR
        (user_id IS NULL AND bank_id IS NOT NULL)
    ),
    CHECK (reason IN (
        'account_creation_bonus',       -- +15€ ao usuário na criação
        'bank_new_user',                -- +145€ ao banco na criação de conta
        'daily_bonus',                  -- +5€ ao usuário (se <= 140€)
        'daily_bonus_payout',           -- -5€ ao banco (contrapartida do bónus)
        'sale_income',                  -- +valor líquido ao vendedor
        'platform_fee',                 -- +15% ao banco
        'official_store_purchase',      -- -valor ao usuário que compra na loja
        'official_store_income',        -- +valor ao banco (compra na loja oficial)
        'account_deletion_transfer',    -- +saldo-15€ ao banco, se aplicável
        'bank_deletion_reversal'        -- -145€ ao banco, na eliminação de conta
    ))
);

CREATE TABLE IF NOT EXISTS deals (
    id BIGSERIAL PRIMARY KEY,
    listing_id BIGINT REFERENCES listings(id) ON DELETE RESTRICT,
    offer_id BIGINT REFERENCES offers(id) ON DELETE RESTRICT,
    seller_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    buyer_id BIGINT REFERENCES users(id) ON DELETE RESTRICT,
    type VARCHAR(20) NOT NULL CHECK (type IN ('sale', 'trade')),
    price_net NUMERIC(10,2),
    price_gross NUMERIC(10,2),
    platform_fee NUMERIC(10,2),
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (
        (type = 'sale' AND offer_id IS NULL AND price_net IS NOT NULL AND price_gross IS NOT NULL AND platform_fee IS NOT NULL)
        OR
        (type = 'trade' AND offer_id IS NOT NULL AND price_net IS NULL AND price_gross IS NULL AND platform_fee IS NULL)
    )
);

CREATE TABLE IF NOT EXISTS receipts (
    id BIGSERIAL PRIMARY KEY,
    deal_id BIGINT REFERENCES deals(id) ON DELETE RESTRICT UNIQUE,
    receipt_number VARCHAR(30) NOT NULL UNIQUE,
    sent_to_buyer_at TIMESTAMP,
    sent_to_seller_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS email_verification_tokens (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS status_incidents(
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    severity VARCHAR(20) NOT NULL CHECK (severity IN ('minor', 'major', 'critical')),
    status VARCHAR(20) NOT NULL DEFAULT 'investigating' CHECK (status IN ('investigating', 'identified', 'monitoring', 'resolved')),
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP
);