from fastapi import APIRouter, Depends, HTTPException, FastAPI
from sqlalchemy.orm import Session
from sqlalchemy import text
from sqlalchemy.exc import SQLAlchemyError
from app.schemas import ProjectAllocationRequest
from app.services.allocation_service import EventTeamAllocator
from app.db import get_db, engine, Base

router = APIRouter()
allocator = EventTeamAllocator()

# Move the table creation logic to a FastAPI startup event
def on_startup():
    Base.metadata.create_all(bind=engine)

app = FastAPI(on_startup=[on_startup])

@router.get("/test")
def test_endpoint(db: Session = Depends(get_db)):
    try:
        db.execute(text("SELECT 1"))
        return {"message": "API is working and database connection is successful!"}
    except SQLAlchemyError as e:
        return {"error": "Database connection failed!", "details": str(e)}

app.include_router(router)
