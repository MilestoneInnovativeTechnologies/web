{!! formGroup(2,'ratings','static','Ratings',($Data->Feedback)?$Data->Feedback->points:0, ['labelWidth' => 4]) !!}
{!! formGroup(2,'feedback','textarea','Feedback/Comments',($Data->Feedback)?$Data->Feedback->feedback:'', ['labelWidth' => 4, 'style' => 'height:150px']) !!}
<input type="hidden" name="customer" value="{{ $Data->customer }}">
<input type="hidden" name="points" value="{{ ($Data->Feedback)?$Data->Feedback->points:0 }}">