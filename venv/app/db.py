from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from app.utils.ssh_tunnel import SSH_TUNNEL
from app.config import Config

DATABASE_URL = (
    f"mysql+pymysql://{Config.DATABASE_USER}:{Config.DATABASE_PASSWORD}"
    f"@127.0.0.1:{SSH_TUNNEL.local_bind_port}/{Config.DATABASE_NAME}"
)

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
