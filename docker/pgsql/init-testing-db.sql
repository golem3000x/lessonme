-- Create testing database
CREATE DATABASE testing;

-- Grant privileges to the same user
GRANT ALL PRIVILEGES ON DATABASE testing TO sail;

-- Connect to testing database and create vector extension
\c testing;
CREATE EXTENSION IF NOT EXISTS vector;
