from fastapi import APIRouter, HTTPException, Depends
from app.database import get_db
from app.schemas import ProjectAllocationRequest
from app.allocator import EventTeamAllocator
import logging

router = APIRouter()
allocator = EventTeamAllocator()

logger = logging.getLogger(__name__)

@router.post("/allocate-teams")
def allocate_teams(request: ProjectAllocationRequest, db=Depends(get_db)):
    logger.info("Received allocation request: %s", request)
    try:
        result = allocator.allocate_teams(
            db,
            request.project_name,
            request.package_id,
            request.start,
            request.end,
        )
        logger.info("Allocation result: %s", result)
        return result
    except Exception as e:
        logger.error("Error during team allocation: %s", str(e))
        raise HTTPException(status_code=400, detail=f"Error: {str(e)}")


@router.get("/allocated-teams/{project_name}")
def get_allocated_teams(project_name: str):
    logger.info("Fetching allocated teams for project: %s", project_name)
    if project_name in allocator.allocated_teams:
        return allocator.allocated_teams[project_name]
    else:
        logger.warning("No allocated teams found for project: %s", project_name)
        raise HTTPException(
            status_code=404, detail=f"No allocated teams found for project '{project_name}'"
        )
