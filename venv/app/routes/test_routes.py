from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.database import SessionLocal
from sqlalchemy.exc import SQLAlchemyError

router = APIRouter()

# Dependency to get the DB session
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

@router.get("/test")
def test_endpoint(db: Session = Depends(get_db)):
    try:
        db.execute("SELECT 1")
        return {"message": "API is working and database connection is successful!"}
    except SQLAlchemyError as e:
        return {"error": "Database connection failed!", "details": str(e)}
