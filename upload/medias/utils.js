function padLeft(Content, PadLength, PadChar)
{
	var ContentString = Content.toString();
	var PadToLenght = PadLength - ContentString.length;
	
	if (PadToLenght > 0)
		for (i = 0; i < PadToLenght; i++)
			ContentString = PadChar + ContentString;
			
	return ContentString;
}

function toTimeHours(seconds)
{
	var t_minutes = 0, t_hours = 0;
	
	if (seconds >= (60 * 60))
	{
		t_hours = Math.floor(seconds / (60 * 60));
		seconds = seconds - (t_hours * (60 * 60));
	}
	
	if (seconds >= 60)
	{
		t_minutes = Math.floor(seconds / 60);
		seconds = seconds - (t_minutes * 60);
	}

	return padLeft(t_hours, 2, "0") + ":" + padLeft(t_minutes, 2, "0") + ":" + padLeft(seconds, 2, "0");
}