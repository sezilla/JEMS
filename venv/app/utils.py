import logging
from datetime import datetime, timedelta

logger = logging.getLogger(__name__)

def parse_date(date_str: str) -> datetime:
    """
    Converts a date string to a datetime object.
    """
    try:
        return datetime.strptime(date_str, '%Y-%m-%d')
    except ValueError as e:
        logger.error(f"Invalid date format: {date_str}")
        raise ValueError(f"Invalid date format: {date_str}. Expected 'YYYY-MM-DD'.")

def validate_date_range(start: str, end: str) -> bool:
    """
    Validates that the start date is before the end date.
    """
    start_date = parse_date(start)
    end_date = parse_date(end)
    if start_date >= end_date:
        logger.error(f"Invalid date range: start ({start}) is not before end ({end}).")
        raise ValueError("Start date must be before end date.")
    return True

def log_error_and_raise(message: str, exception_type=ValueError):
    """
    Logs an error message and raises an exception.
    """
    logger.error(message)
    raise exception_type(message)

def get_available_teams(team_schedules: dict, start: str, end: str) -> list:
    """
    Checks which teams are available between the specified dates.
    """
    start_dt = parse_date(start)
    end_dt = parse_date(end)

    available_teams = [
        team_id for team_id, schedules in team_schedules.items()
        if all(end_dt < s or start_dt > e for s, e in schedules)
    ]

    logger.debug(f"Available teams between {start} and {end}: {available_teams}")
    return available_teams
