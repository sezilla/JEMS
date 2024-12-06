from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from app.config import Config
from app.utils.ssh_tunnel import SSH_TUNNEL

Base = declarative_base()
SessionLocal = None  # Initialize later


def get_database_url():
    if SSH_TUNNEL is None or not SSH_TUNNEL.is_active:
        raise RuntimeError("SSH tunnel is not active. Start the tunnel before accessing the database.")
    
    return (
        f"mysql+pymysql://{Config.DATABASE_USER}:{Config.DATABASE_PASSWORD}"
        f"@127.0.0.1:{SSH_TUNNEL.local_bind_port}/{Config.DATABASE_NAME}"
    )


def initialize_database():
    global SessionLocal
    engine = create_engine(get_database_url())
    SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)


def get_db():
    if SessionLocal is None:
        raise RuntimeError("Database session is not initialized. Ensure the SSH tunnel is running.")
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
