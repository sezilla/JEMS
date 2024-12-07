from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.schemas import ProjectAllocationRequest
from app.services.allocation_service import EventTeamAllocator
from app.db import get_db

router = APIRouter()
allocator = EventTeamAllocator()

@router.get("/project-history")
def get_project_history():
    # Return project history
    return {}