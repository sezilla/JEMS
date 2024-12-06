from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.schemas import ProjectAllocationRequest
from app.services.allocation_service import EventTeamAllocator
from app.db import get_db

router = APIRouter()
allocator = EventTeamAllocator()

@router.get("/")
def get_project_history():
    return allocator.project_history