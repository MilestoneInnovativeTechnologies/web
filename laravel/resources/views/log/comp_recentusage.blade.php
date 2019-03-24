<div class="table table-responsive" style="font-size: 9px;">
    <table class="table striped">
        <tbody>
        @forelse($log as $date => $sva)
            <tr><th>{{ $date }}</th><td>
                    @forelse($sva as $software => $va)
                        <strong>{{ $software }}</strong><br>
                        @forelse($va as $version => $array)
                            <a style="width: 3px">&nbsp;</a> - <i><strong>{{ $version }}</strong></i><br>
                            <div style="margin-left: 8px">{{ implode(", ",array_column($array,'time')) }}</div>
                        @empty
                            no entries for this software
                        @endforelse
                        @empty
                        no entries for the day
                    @endforelse
                </td></tr>
            @empty
            <tr><th>No entries yet</th></tr>
        @endforelse
        </tbody>
    </table>
</div>