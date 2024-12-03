import os
from dotenv import load_dotenv

load_dotenv()

DATABASE_HOST = os.getenv("DB_HOST")
DATABASE_PORT = int(os.getenv("DB_PORT", "3306"))
DATABASE_NAME = os.getenv("DB_DATABASE")
DATABASE_USER = os.getenv("DB_USERNAME")
DATABASE_PASSWORD = os.getenv("DB_PASSWORD")
SSH_HOST = os.getenv("SSH_HOST")
SSH_USER = os.getenv("SSH_USERNAME")
SSH_PRIVATE_KEY_PATH = "/etc/secrets/jem"  # Replace with Render's secret file path
