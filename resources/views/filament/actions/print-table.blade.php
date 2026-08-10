<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>{{ $title }}</title>
        <style>
            @page { margin: 1.2cm; }
            * { box-sizing: border-box; }
            body { margin: 0; color: #111827; font: 12px/1.45 sans-serif; }
            h1 { margin: 0 0 4px; font-size: 20px; }
            p { margin: 0 0 20px; color: #6b7280; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 7px 8px; border: 1px solid #d1d5db; text-align: start; vertical-align: top; }
            th { background: #f3f4f6; font-weight: 700; }
            tr { break-inside: avoid; }
        </style>
    </head>
    <body>
        <h1>{{ $title }}</h1>
        <p>{{ __('table-output.print.generated_at', ['date' => now()->format('d/m/Y H:i')]) }}</p>

        <table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column->getLabel() }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        @foreach ($columns as $column)
                            <td>{{ $column->getState($record) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}">{{ __('table-output.print.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
