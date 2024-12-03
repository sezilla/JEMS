from fastapi import APIRouter, Depends, HTTPException
from app.database import get_db
from app.schemas import ProjectAllocationRequest
from app.services import allocator
import logging

router = APIRouter()
logger = logging.getLogger(__name__)

@router.post("/")
def allocate_teams(request: ProjectAllocationRequest, db=Depends(get_db)):
    try:
        result = allocator.allocate_teams(
            db,
            request.project_name,
            request.package_id,
            request.start,
            request.end
        )
        return result
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))
