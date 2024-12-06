from pydantic import BaseModel

class ProjectAllocationRequest(BaseModel):
    project_name: str
    package_id: int
    start: str
    end: str

class ProjectAllocationResponse(BaseModel):
    project_name: str
    package_id: int
    start: str
    end: str
    allocated_teams: dict
