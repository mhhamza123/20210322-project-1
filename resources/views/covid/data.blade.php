@extends('layouts.app')
@section('content')
    @foreach($data as $country => $records)
        <div>
            <div class="title m-b-md">
                {{ $country }}
            </div>

            <div class="links">
            
                <table class="table">
                    <thead>
                        <tr>
                            <th> index </th>
                            <th> date </th>
                            <th> country </th>
                            <th> province/state </th>
                            <th> cases </th>
                            <th> increase in cases </th>
                            <th> active cases </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($records as $key => $row)
                            <tr>
                                <td scope="row">{{ $key+1 }}</td>
                                <td>{{ $row['Last_Update'] }}</td>
                                <td>{{ $row['Country_Region'] }}</td>
                                <td>{{ $row['Combined_Key'] }}</td>
                                <td>{{ $row['Confirmed'] }}</td>
                                <td>{{ $row['increaseInCases'] }}</td>
                                <td>{{ $row['Active'] }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection