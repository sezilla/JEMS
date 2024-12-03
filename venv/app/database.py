from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
import os
from app.core.ssh_tunnel import ssh_tunnel

DATABASE_URL = f"mysql+pymysql://{os.getenv('DB_USERNAME')}:{os.getenv('DB_PASSWORD')}@127.0.0.1:3306/{os.getenv('DB_DATABASE')}"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Dependency
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
