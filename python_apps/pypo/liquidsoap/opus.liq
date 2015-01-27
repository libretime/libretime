            if bitrate == 24 then 
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 24, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 24, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 32 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 32, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 32, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 48 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 48, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 48, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 64 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 64, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 64, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 96 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 96, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 96, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 128 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 128, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 128, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 160 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 160, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 160, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 192 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 192, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 192, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 224 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 224, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 224, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 256 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 256, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 256, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            elsif bitrate == 320 then
                if stereo then
                    ignore(output_stereo(%opus(bitrate = 320, channels = 2, signal="music", application="audio", complexity=10, vbr="constrained"), !source))
                else
                    ignore(output_mono(%opus(bitrate = 320, channels = 1, signal="music", application="audio", complexity=10, vbr="constrained"), mean(!source)))
                end
            end

