# Admin flight creation fix

Flight duration and available seats are generated on the server. Automatic flight number generation no longer relies on MySQL-specific SQL, so it works with the repository's default SQLite setup.
