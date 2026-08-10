<div
    x-data
    x-on:print-table.window="
        const documentToPrint = $refs.frame.contentWindow?.document

        if (! documentToPrint) {
            return
        }

        documentToPrint.open()
        documentToPrint.write($event.detail.html)
        documentToPrint.close()
        $refs.frame.contentWindow.focus()
        $refs.frame.contentWindow.print()
    "
>
    <iframe
        x-ref="frame"
        title="{{ __('table-output.print.frame_title') }}"
        class="fi-hidden"
        aria-hidden="true"
    ></iframe>
</div>
